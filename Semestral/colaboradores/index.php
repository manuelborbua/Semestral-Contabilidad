<?php
declare(strict_types=1);

$titulo_pagina = 'Colaboradores';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/utilidades.php';

$colaboradores = [];
$error = null;
$busqueda = trim($_GET['q'] ?? '');

try {
    $db = exigirConexion($conexion);
    if ($busqueda !== '') {
        $termino = '%' . $busqueda . '%';
        $stmt = $db->prepare(
            'SELECT * FROM colaboradores WHERE nombre_completo LIKE ? OR cedula LIKE ? ORDER BY nombre_completo'
        );
        $stmt->bind_param('ss', $termino, $termino);
        $stmt->execute();
        $colaboradores = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        $colaboradores = $db->query('SELECT * FROM colaboradores ORDER BY nombre_completo')->fetch_all(MYSQLI_ASSOC);
    }
} catch (Throwable $e) {
    $error = $e;
}

require __DIR__ . '/../includes/header.php';
?>
<div class="encabezado-pagina">
    <div>
        <h1>Colaboradores</h1>
        <p>Personal registrado en La Prospera, S.A.</p>
    </div>
    <a class="boton boton-dorado no-imprimir" href="crear.php">Registrar colaborador</a>
</div>

<?php if ($error): mostrarErrorConexion($error); endif; ?>
<?php if (isset($_GET['creado'])): ?><div class="mensaje mensaje-exito">Colaborador registrado correctamente.</div><?php endif; ?>
<?php if (isset($_GET['editado'])): ?><div class="mensaje mensaje-exito">Los datos del colaborador fueron actualizados.</div><?php endif; ?>
<?php if (isset($_GET['eliminado'])): ?><div class="mensaje mensaje-exito">Colaborador eliminado correctamente.</div><?php endif; ?>
<?php if (isset($_GET['error'])): ?><div class="mensaje mensaje-error"><?= escapar($_GET['error']) ?></div><?php endif; ?>

<form method="GET" class="barra-busqueda no-imprimir">
    <input type="search" name="q" value="<?= escapar($busqueda) ?>" placeholder="Buscar por nombre o cédula">
    <button class="boton boton-primario" type="submit">Buscar</button>
    <?php if ($busqueda !== ''): ?><a class="boton boton-secundario" href="index.php">Limpiar</a><?php endif; ?>
</form>

<div class="tabla-responsive">
<table class="tabla-datos">
    <thead><tr><th>Nombre</th><th>Cédula</th><th>Cargo</th><th>Salario mensual</th><th>Declaración</th><th class="no-imprimir">Acciones</th></tr></thead>
    <tbody>
    <?php if (!$colaboradores): ?>
        <tr><td colspan="6">No hay colaboradores para mostrar.</td></tr>
    <?php endif; ?>
    <?php foreach ($colaboradores as $fila): ?>
        <tr>
            <td><?= escapar($fila['nombre_completo']) ?></td>
            <td><?= escapar($fila['cedula']) ?></td>
            <td><?= escapar($fila['cargo']) ?></td>
            <td><?= formatoDinero($fila['salario_base']) ?></td>
            <td><?= escapar($fila['tipo_declaracion']) ?></td>
            <td class="no-imprimir acciones">
                <a href="editar.php?id=<?= (int) $fila['id'] ?>">Editar</a>
                <form method="POST" action="eliminar.php" onsubmit="return confirm('¿Eliminar este colaborador?');">
                    <input type="hidden" name="id" value="<?= (int) $fila['id'] ?>">
                    <button type="submit" class="enlace-peligro">Eliminar</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>

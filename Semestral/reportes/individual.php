<?php
declare(strict_types=1);

$titulo_pagina = 'Reporte individual';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/utilidades.php';

$fila = null;
$opciones = [];
$error = null;
try {
    $db = exigirConexion($conexion);
    $opciones = $db->query(
        'SELECT p.id, p.periodo, c.nombre_completo
         FROM planillas p JOIN colaboradores c ON c.id=p.colaborador_id
         ORDER BY c.nombre_completo, p.fecha_fin DESC'
    )->fetch_all(MYSQLI_ASSOC);
    $id = (int) ($_GET['id'] ?? 0);
    if ($id > 0) {
        $stmt = $db->prepare('SELECT p.*, c.nombre_completo, c.cedula, c.estado_civil, c.cargo, c.salario_base FROM planillas p JOIN colaboradores c ON c.id=p.colaborador_id WHERE p.id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $fila = $stmt->get_result()->fetch_assoc();
    } else {
        $fila = $db->query('SELECT p.*, c.nombre_completo, c.cedula, c.estado_civil, c.cargo, c.salario_base FROM planillas p JOIN colaboradores c ON c.id=p.colaborador_id ORDER BY p.fecha_creacion DESC LIMIT 1')->fetch_assoc();
    }
} catch (Throwable $e) {
    $error = $e;
}

ob_start();
?>
<?php if ($fila): ?>
<article class="reporte" id="contenido-reporte">
    <header class="cabecera-reporte"><h2>La Prospera, S.A.</h2><p>Comprobante individual de planilla</p><p><?= escapar($fila['periodo']) ?></p></header>
    <div class="datos-empleado"><p><strong>Colaborador:</strong> <?= escapar($fila['nombre_completo']) ?></p><p><strong>Cédula:</strong> <?= escapar($fila['cedula']) ?></p><p><strong>Cargo:</strong> <?= escapar($fila['cargo']) ?></p><p><strong>Estado civil:</strong> <?= escapar($fila['estado_civil']) ?></p></div>
    <div class="dos-columnas-reporte">
        <table class="tabla-datos"><thead><tr><th>Ingresos</th><th>Monto</th></tr></thead><tbody>
            <tr><td>Salario quincenal</td><td><?= formatoDinero($fila['salario_quincenal']) ?></td></tr>
            <tr><td>Bonificación</td><td><?= formatoDinero($fila['bonificacion']) ?></td></tr>
            <tr><td>Horas extra</td><td><?= formatoDinero($fila['horas_extras']) ?></td></tr>
            <tr><td>Comisión</td><td><?= formatoDinero($fila['comision']) ?></td></tr>
            <tr><td>Dieta</td><td><?= formatoDinero($fila['dieta']) ?></td></tr>
            <tr><td>Otros ingresos</td><td><?= formatoDinero($fila['otros_ingresos']) ?></td></tr>
            <tr class="fila-total"><td>Salario bruto</td><td><?= formatoDinero($fila['salario_bruto']) ?></td></tr>
        </tbody></table>
        <table class="tabla-datos"><thead><tr><th>Deducciones</th><th>Monto</th></tr></thead><tbody>
            <tr><td>Seguro Social</td><td><?= formatoDinero($fila['seguro_social']) ?></td></tr>
            <tr><td>Seguro Educativo</td><td><?= formatoDinero($fila['seguro_educativo']) ?></td></tr>
            <tr><td>Impuesto sobre la renta</td><td><?= formatoDinero($fila['impuesto_renta']) ?></td></tr>
            <tr><td>Otros descuentos</td><td><?= formatoDinero($fila['otros_descuentos']) ?></td></tr>
            <tr class="fila-total"><td>Total descuentos</td><td><?= formatoDinero($fila['total_descuentos']) ?></td></tr>
            <tr class="fila-neto"><td>Salario neto</td><td><?= formatoDinero($fila['salario_neto']) ?></td></tr>
        </tbody></table>
    </div>
</article>
<?php endif; ?>
<?php $contenido_reporte = ob_get_clean(); ?>
<?php require __DIR__ . '/../includes/header.php'; ?>
<div class="encabezado-pagina no-imprimir"><div><h1>Reporte individual</h1><p>Detalle de ingresos y deducciones.</p></div></div>
<?php if ($error): mostrarErrorConexion($error); elseif (!$fila): ?><div class="mensaje mensaje-info">Primero calcule una planilla.</div><?php else: ?>
<form method="GET" class="selector-reporte no-imprimir">
    <label for="id"><strong>Seleccionar colaborador:</strong></label>
    <select name="id" id="id" onchange="this.form.submit()">
        <?php foreach ($opciones as $opcion): ?>
            <option value="<?= (int) $opcion['id'] ?>" <?= (int) $opcion['id'] === (int) $fila['id'] ? 'selected' : '' ?>>
                <?= escapar($opcion['nombre_completo'] . ' — ' . $opcion['periodo']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <noscript><button class="boton boton-secundario" type="submit">Ver reporte</button></noscript>
</form>
<?= $contenido_reporte ?>
<?php require __DIR__ . '/acciones.php'; ?>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>

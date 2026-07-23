<?php
declare(strict_types=1);

$titulo_pagina = 'Editar colaborador';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/utilidades.php';

$error = '';
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

try {
    $db = exigirConexion($conexion);
    if ($id <= 0) {
        throw new InvalidArgumentException('Colaborador no válido.');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = trim($_POST['nombre_completo'] ?? '');
        $cedula = trim($_POST['cedula'] ?? '');
        $estado = trim($_POST['estado_civil'] ?? '');
        $cargo = trim($_POST['cargo'] ?? '');
        $salario = (float) ($_POST['salario_base'] ?? 0);
        $anio = (int) ($_POST['anio_inicio'] ?? 0);
        $declaracion = ($_POST['tipo_declaracion'] ?? '') === 'Conjunta' ? 'Conjunta' : 'Individual';
        validarDatosColaborador($nombre, $cedula, $estado, $cargo, $salario, $anio);
        $stmt = $db->prepare('UPDATE colaboradores SET nombre_completo=?, cedula=?, estado_civil=?, cargo=?, salario_base=?, anio_inicio=?, tipo_declaracion=? WHERE id=?');
        $stmt->bind_param('ssssdisi', $nombre, $cedula, $estado, $cargo, $salario, $anio, $declaracion, $id);
        $stmt->execute();
        header('Location: index.php?editado=1');
        exit;
    }

    $stmt = $db->prepare('SELECT * FROM colaboradores WHERE id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $colaborador = $stmt->get_result()->fetch_assoc();
    if (!$colaborador) {
        throw new RuntimeException('El colaborador solicitado no existe.');
    }
} catch (mysqli_sql_exception $e) {
    $error = $e->getCode() === 1062
        ? 'Ya existe un colaborador con esa cédula y nombre.'
        : 'No fue posible actualizar al colaborador.';
    $colaborador = $_POST;
} catch (Throwable $e) {
    $error = $e->getMessage();
    $colaborador = $_POST ?: [];
}

require __DIR__ . '/../includes/header.php';
?>
<div class="encabezado-pagina"><div><h1>Editar colaborador</h1><p>Actualice los datos del expediente.</p></div></div>
<?php if ($error): ?><div class="mensaje mensaje-error"><?= escapar($error) ?></div><?php endif; ?>
<?php if ($colaborador): require __DIR__ . '/formulario.php'; endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>

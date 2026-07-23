<?php
declare(strict_types=1);

$titulo_pagina = 'Registrar colaborador';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/utilidades.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = exigirConexion($conexion);
        $nombre = trim($_POST['nombre_completo'] ?? '');
        $cedula = trim($_POST['cedula'] ?? '');
        $estado = trim($_POST['estado_civil'] ?? '');
        $cargo = trim($_POST['cargo'] ?? '');
        $salario = (float) ($_POST['salario_base'] ?? 0);
        $anio = (int) ($_POST['anio_inicio'] ?? 0);
        $declaracion = ($_POST['tipo_declaracion'] ?? '') === 'Conjunta' ? 'Conjunta' : 'Individual';

        validarDatosColaborador($nombre, $cedula, $estado, $cargo, $salario, $anio);

        $stmt = $db->prepare('INSERT INTO colaboradores (nombre_completo, cedula, estado_civil, cargo, salario_base, anio_inicio, tipo_declaracion) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssdis', $nombre, $cedula, $estado, $cargo, $salario, $anio, $declaracion);
        $stmt->execute();
        header('Location: index.php?creado=1');
        exit;
    } catch (mysqli_sql_exception $e) {
        $error = $e->getCode() === 1062
            ? 'Ya existe un colaborador con esa cédula y nombre.'
            : 'No fue posible registrar al colaborador.';
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

require __DIR__ . '/../includes/header.php';
?>
<div class="encabezado-pagina"><div><h1>Registrar colaborador</h1><p>Complete el expediente básico del trabajador.</p></div></div>
<?php if ($error): ?><div class="mensaje mensaje-error"><?= escapar($error) ?></div><?php endif; ?>
<?php require __DIR__ . '/formulario.php'; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>

<?php
declare(strict_types=1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host_bd = getenv('DB_HOST');
$puerto_bd = (int) getenv('DB_PORT');
$usuario_bd = getenv('DB_USER');
$clave_bd = getenv('DB_PASSWORD');
$nombre_bd = getenv('DB_NAME');

$conexion = null;

try {
    if (
        !$host_bd ||
        !$puerto_bd ||
        !$usuario_bd ||
        $clave_bd === false ||
        !$nombre_bd
    ) {
        throw new RuntimeException(
            'Faltan variables DB_* en el servicio PHP. ' .
            'DB_HOST=' . var_export($host_bd, true) . ', ' .
            'DB_PORT=' . var_export($puerto_bd, true) . ', ' .
            'DB_USER=' . var_export($usuario_bd, true) . ', ' .
            'DB_NAME=' . var_export($nombre_bd, true)
        );
    }

    $conexion = new mysqli(
        $host_bd,
        $usuario_bd,
        $clave_bd,
        $nombre_bd,
        $puerto_bd
    );

    $conexion->set_charset('utf8mb4');

} catch (Throwable $e) {
    $conexion = null;

    error_log('ERROR BD: ' . $e->getMessage());

    // Solo temporalmente para diagnóstico.
    die(
        '<pre>Error de conexión: ' .
        htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') .
        '</pre>'
    );
}
?>

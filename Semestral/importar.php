<?php

declare(strict_types=1);

require_once __DIR__ . '/config/conexion.php';

if (!$conexion) {
    exit('No fue posible conectar con la base de datos.');
}

$rutaSql = __DIR__ . '/database/planilla.sql';
$sql = file_get_contents($rutaSql);

if ($sql === false) {
    exit('No se pudo leer database/planilla.sql.');
}

try {
    if (!$conexion->multi_query($sql)) {
        throw new RuntimeException($conexion->error);
    }

    do {
        if ($resultado = $conexion->store_result()) {
            $resultado->free();
        }

        if (!$conexion->more_results()) {
            break;
        }
    } while ($conexion->next_result());

    echo 'Base de datos importada correctamente.';
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Error al importar: ' .
        htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>

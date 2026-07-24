<?php

require_once __DIR__ . '/config/conexion.php';

if (!$conexion) {
    die("No fue posible conectar con la base de datos.");
}

$sql = file_get_contents(__DIR__ . '/Script_DataBase');

if ($sql === false) {
    die("No se pudo leer Script_DataBase.");
}

if ($conexion->multi_query($sql)) {

    do {
        if ($resultado = $conexion->store_result()) {
            $resultado->free();
        }
    } while ($conexion->more_results() && $conexion->next_result());

    echo "<h2>Base de datos importada correctamente.</h2>";

} else {

    echo "<h2>Error:</h2>";
    echo $conexion->error;

}

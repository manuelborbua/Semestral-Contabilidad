<?php<?php

$host_bd     = 'localhost';   
$usuario_bd  = 'root';        
$clave_bd    = '';            
$nombre_bd   = 'planilla_prospera'; 

$conexion = null;
try {
    $conexion = mysqli_connect($host_bd, $usuario_bd, $clave_bd, $nombre_bd);
    mysqli_set_charset($conexion, 'utf8mb4');
} 
catch (mysqli_sql_exception $e) {

    $conexion = null;
}

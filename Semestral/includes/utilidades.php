<?php
declare(strict_types=1);

function escapar(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function formatoDinero(float|string $valor): string
{
    return 'B/. ' . number_format((float) $valor, 2);
}

function exigirConexion(?mysqli $conexion): mysqli
{
    if (!$conexion instanceof mysqli) {
        http_response_code(503);
        throw new RuntimeException(
            'No hay conexión con la base de datos. Importe database/planilla.sql y revise las variables DB_*.'
        );
    }

    return $conexion;
}

function mostrarErrorConexion(Throwable $error): void
{
    echo '<div class="mensaje mensaje-error">' . escapar($error->getMessage()) . '</div>';
}

function validarDatosColaborador(
    string $nombre,
    string $cedula,
    string $estado,
    string $cargo,
    float $salario,
    int $anio
): void {
    if ($nombre === '' || $cedula === '' || $estado === '' || $cargo === '' || $salario <= 0 || $anio < 1900) {
        throw new InvalidArgumentException('Complete correctamente todos los campos obligatorios.');
    }
    if (!preg_match('/^[0-9]{1,2}-[0-9]{2,4}-[0-9]{1,6}$/', $cedula)) {
        throw new InvalidArgumentException('La cédula debe tener un formato como 8-123-4567.');
    }
    if ($anio > (int) date('Y')) {
        throw new InvalidArgumentException('El año de inicio no puede ser posterior al año actual.');
    }
}

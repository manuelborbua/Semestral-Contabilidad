<?php

$titulo_pagina = 'Inicio';
$total_colaboradores = 0;
$total_planillas_periodo = 0;
$conexion_disponible = false;

$ruta_conexion = __DIR__ . '/config/conexion.php';
if (file_exists($ruta_conexion)) {
    require_once $ruta_conexion;
    if (isset($conexion) && $conexion instanceof mysqli) {
        $conexion_disponible = true;
        $r1 = @mysqli_query($conexion, "SELECT COUNT(*) AS total FROM colaboradores");
        if ($r1) { $total_colaboradores = (int) mysqli_fetch_assoc($r1)['total']; }

        $r2 = @mysqli_query($conexion, "SELECT COUNT(*) AS total FROM planillas");
        if ($r2) { $total_planillas_periodo = (int) mysqli_fetch_assoc($r2)['total']; }
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="encabezado-pagina">
    <div>
        <h1>Bienvenido a Planilla Prospera</h1>
        <p>Panel general del sistema de planillas — Grupo 5</p>
    </div>
</div>

<?php if (!$conexion_disponible): ?>
    <div class="mensaje mensaje-info">
        La conexión a la base de datos aún no está disponible. Los contadores se mostrarán en cero hasta que el módulo de datos esté integrado.
    </div>
<?php endif; ?>

<section class="rejilla-tarjetas">
    <div class="tarjeta">
        <div class="tarjeta__etiqueta">Colaboradores registrados</div>
        <div class="tarjeta__valor"><?php echo $total_colaboradores; ?></div>
    </div>
    <div class="tarjeta">
        <div class="tarjeta__etiqueta">Planillas calculadas</div>
        <div class="tarjeta__valor acento"><?php echo $total_planillas_periodo; ?></div>
    </div>
    <div class="tarjeta">
        <div class="tarjeta__etiqueta">Periodo actual</div>
        <div class="tarjeta__valor" style="font-size:1.3rem;"><?php echo date('d/m/Y'); ?></div>
    </div>
</section>

<h2>Accesos rápidos</h2>
<div class="accesos-rapidos">
    <a class="acceso-rapido" href="colaboradores/index.php">
        Colaboradores
        <small>Ver y buscar el listado de personal</small>
    </a>
    <a class="acceso-rapido" href="colaboradores/crear.php">
        Registrar colaborador
        <small>Agregar un nuevo colaborador</small>
    </a>
    <a class="acceso-rapido" href="planilla/calcular.php">
        Calcular planilla
        <small>Procesar la quincena actual</small>
    </a>
    <a class="acceso-rapido" href="reportes/individual.php">
        Reporte individual
        <small>Ver detalle por colaborador</small>
    </a>
    <a class="acceso-rapido" href="reportes/grupal.php">
        Reporte grupal
        <small>Tabla general de la planilla</small>
    </a>
    <a class="acceso-rapido" href="reportes/css.php">
        Reporte CSS
        <small>Reporte para la Caja de Seguro Social</small>
    </a>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>

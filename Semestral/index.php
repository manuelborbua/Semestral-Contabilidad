<?php

$titulo_pagina = 'Inicio';
require_once __DIR__ . '/config/parametros.php';
$total_colaboradores = 0;
$total_planillas_periodo = 0;
$conexion_disponible = false;

$ruta_conexion = __DIR__ . '/config/conexion.php';
if (file_exists($ruta_conexion)) {
    require_once $ruta_conexion;
    if (isset($conexion) && $conexion instanceof mysqli) {
        try {
            $r1 = $conexion->query("SELECT COUNT(*) AS total FROM colaboradores");
            $total_colaboradores = (int) $r1->fetch_assoc()['total'];
            $r2 = $conexion->query(
                "SELECT COUNT(*) AS total FROM planillas WHERE fecha_inicio='2026-06-16' AND fecha_fin='2026-06-30'"
            );
            $total_planillas_periodo = (int) $r2->fetch_assoc()['total'];
            $conexion_disponible = true;
        } catch (mysqli_sql_exception $e) {
            $conexion_disponible = false;
        }
    }
}

require __DIR__ . '/includes/header.php';
?>

<section class="hero-inicio">
    <div class="hero-inicio__contenido">
        <span class="etiqueta-superior">Sistema de gestión de planillas</span>
        <h1>Control claro de cada pago quincenal.</h1>
        <p>Administra colaboradores, procesa los cálculos del Grupo 5 y genera todos los reportes desde un solo lugar.</p>
        <div class="hero-inicio__acciones no-imprimir">
            <a class="boton boton-dorado" href="planilla/calcular.php">Procesar planilla</a>
            <a class="boton boton-claro" href="reportes/grupal.php">Ver reporte grupal</a>
        </div>
    </div>
    <div class="hero-inicio__periodo">
        <span>Periodo activo</span>
        <strong>16 — 30</strong>
        <p>Junio de 2026</p>
        <div class="periodo-estado"><i></i> Listo para consultar</div>
    </div>
</section>

<?php if (!$conexion_disponible): ?>
    <div class="mensaje mensaje-info">
        La conexión a la base de datos aún no está disponible. Los contadores se mostrarán en cero hasta que el módulo de datos esté integrado.
    </div>
<?php endif; ?>

<section class="rejilla-tarjetas">
    <div class="tarjeta tarjeta-metrica">
        <span class="tarjeta__icono" aria-hidden="true">
            <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </span>
        <div>
            <div class="tarjeta__etiqueta">Colaboradores</div>
            <div class="tarjeta__valor"><?php echo $total_colaboradores; ?></div>
            <small>Personal registrado</small>
        </div>
    </div>
    <div class="tarjeta tarjeta-metrica">
        <span class="tarjeta__icono dorado" aria-hidden="true">
            <svg viewBox="0 0 24 24"><path d="M9 11l3 3L22 4M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        </span>
        <div>
            <div class="tarjeta__etiqueta">Planillas procesadas</div>
            <div class="tarjeta__valor"><?php echo $total_planillas_periodo; ?></div>
            <small>En el periodo actual</small>
        </div>
    </div>
    <div class="tarjeta tarjeta-metrica">
        <span class="tarjeta__icono verde" aria-hidden="true">
            <svg viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </span>
        <div>
            <div class="tarjeta__etiqueta">Estado del periodo</div>
            <div class="tarjeta__valor tarjeta__valor--texto"><?php echo $total_planillas_periodo >= 4 ? 'Completo' : 'Pendiente'; ?></div>
            <small><?php echo $total_planillas_periodo; ?> de 4 colaboradores</small>
        </div>
    </div>
</section>

<div class="titulo-seccion">
    <div>
        <span>Herramientas</span>
        <h2>Accesos rápidos</h2>
    </div>
    <p>Selecciona una tarea para continuar.</p>
</div>
<div class="accesos-rapidos">
    <a class="acceso-rapido" href="colaboradores/index.php">
        <span class="acceso-rapido__numero">01</span>
        <span><strong>Colaboradores</strong><small>Consulta y administra el personal</small></span>
        <i aria-hidden="true">→</i>
    </a>
    <a class="acceso-rapido" href="colaboradores/crear.php">
        <span class="acceso-rapido__numero">02</span>
        <span><strong>Nuevo colaborador</strong><small>Registra un expediente de personal</small></span>
        <i aria-hidden="true">→</i>
    </a>
    <a class="acceso-rapido" href="planilla/calcular.php">
        <span class="acceso-rapido__numero">03</span>
        <span><strong>Calcular planilla</strong><small>Procesa la segunda quincena</small></span>
        <i aria-hidden="true">→</i>
    </a>
    <a class="acceso-rapido" href="reportes/individual.php">
        <span class="acceso-rapido__numero">04</span>
        <span><strong>Reporte individual</strong><small>Revisa el comprobante detallado</small></span>
        <i aria-hidden="true">→</i>
    </a>
    <a class="acceso-rapido" href="reportes/grupal.php">
        <span class="acceso-rapido__numero">05</span>
        <span><strong>Reporte grupal</strong><small>Consulta la planilla consolidada</small></span>
        <i aria-hidden="true">→</i>
    </a>
    <a class="acceso-rapido" href="reportes/css.php">
        <span class="acceso-rapido__numero">06</span>
        <span><strong>Reporte CSS</strong><small>Visualiza las cuotas reportadas</small></span>
        <i aria-hidden="true">→</i>
    </a>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>

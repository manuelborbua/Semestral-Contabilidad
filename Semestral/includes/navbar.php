<?php

$pagina_actual = basename($_SERVER['PHP_SELF']);
$raiz_proyecto = str_replace('\\', '/', dirname(__DIR__)); // carpeta que contiene includes/
$document_root = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'], '/\\'));
$base = str_replace($document_root, '', $raiz_proyecto);
if ($base === '/' ) { $base = ''; } // proyecto en la raíz del dominio

$enlaces = [
    'Inicio'                 => ['index.php',                 $base . '/index.php'],
    'Colaboradores'          => ['index.php',                 $base . '/colaboradores/index.php'],
    'Registrar colaborador'  => ['crear.php',                 $base . '/colaboradores/crear.php'],
    'Calcular planilla'      => ['calcular.php',               $base . '/planilla/calcular.php'],
    'Historial de planillas' => ['historial.php',              $base . '/planilla/historial.php'],
    'Reporte individual'     => ['individual.php',             $base . '/reportes/individual.php'],
    'Reporte grupal'         => ['grupal.php',                 $base . '/reportes/grupal.php'],
    'Reporte CSS'            => ['css.php',                    $base . '/reportes/css.php'],
];

// Progreso visual de la quincena actual (día 1-15 o 16-fin de mes)
$dia = (int) date('j');
$dias_en_mes = (int) date('t');
if ($dia <= 15) {
    $inicio_q = 1;
    $fin_q = 15;
} else {
    $inicio_q = 16;
    $fin_q = $dias_en_mes;
}
$progreso = (($dia - $inicio_q) / max(1, ($fin_q - $inicio_q))) * 100;
$progreso = max(0, min(100, $progreso));
?>
<header class="barra-superior">
    <div class="barra-superior__marca">
        <strong>Semestral de Contabilidad</strong>
    </div>
    <img src="<?php echo $base; ?>/assets/img/logo.png" alt="Logo Universidad Tecnológica de Panamá" class="logo-universidad">
    <nav>
        <ul class="nav-menu">
            <?php foreach ($enlaces as $texto => $ruta):
                $archivo_comparar = $ruta[0];
                $href = $ruta[1];
                $clase = ($pagina_actual === $archivo_comparar) ? 'activo' : '';
            ?>
            <li><a href="<?php echo $href; ?>" class="<?php echo $clase; ?>"><?php echo htmlspecialchars($texto); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </nav>
</header>

<?php

$raiz_proyecto = str_replace('\\', '/', dirname(__DIR__)); // carpeta que contiene includes/
$document_root = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'], '/\\'));
$base = str_replace($document_root, '', $raiz_proyecto);
if ($base === '/' ) { $base = ''; } // proyecto en la raíz del dominio

$enlaces = [
    'Inicio'                 => ['index.php',                  $base . '/index.php'],
    'Colaboradores'          => ['colaboradores/index.php',    $base . '/colaboradores/index.php'],
    'Registrar colaborador'  => ['colaboradores/crear.php',    $base . '/colaboradores/crear.php'],
    'Calcular planilla'      => ['planilla/calcular.php',      $base . '/planilla/calcular.php'],
    'Historial de planillas' => ['planilla/historial.php',     $base . '/planilla/historial.php'],
    'Reporte individual'     => ['reportes/individual.php',    $base . '/reportes/individual.php'],
    'Reporte grupal'         => ['reportes/grupal.php',        $base . '/reportes/grupal.php'],
    'Reporte CSS'            => ['reportes/css.php',           $base . '/reportes/css.php'],
];
$ruta_solicitada = parse_url($_SERVER['REQUEST_URI'] ?? '/index.php', PHP_URL_PATH);
$ruta_actual = ltrim(substr($ruta_solicitada, strlen($base)), '/');

?>
<header class="barra-superior">
    <div class="barra-superior__interior">
        <a class="barra-superior__marca" href="<?php echo $base; ?>/index.php" aria-label="Ir al inicio">
            <span class="marca-logo">
                <img src="<?php echo $base; ?>/assets/img/logo.png" alt="">
            </span>
            <span class="marca-texto">
                <strong>Planilla Prospera</strong>
                <small>Semestral de Contabilidad · Grupo 5</small>
            </span>
        </a>
        <span class="estado-sistema"><i></i> Sistema local</span>
    </div>
    <nav class="navegacion-principal" aria-label="Navegación principal">
        <ul class="nav-menu">
            <?php foreach ($enlaces as $texto => $ruta):
                $archivo_comparar = $ruta[0];
                $href = $ruta[1];
                $clase = ($ruta_actual === $archivo_comparar) ? 'activo' : '';
            ?>
            <li><a href="<?php echo $href; ?>" class="<?php echo $clase; ?>"><?php echo htmlspecialchars($texto); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </nav>
</header>

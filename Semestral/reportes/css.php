<?php
declare(strict_types=1);

$titulo_pagina = 'Reporte CSS';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/utilidades.php';
$filas = [];
$error = null;
try {
    $db = exigirConexion($conexion);
    $filas = $db->query('SELECT p.*, c.nombre_completo, c.cedula FROM planillas p JOIN colaboradores c ON c.id=p.colaborador_id WHERE p.fecha_inicio="2026-06-16" AND p.fecha_fin="2026-06-30" ORDER BY c.nombre_completo')->fetch_all(MYSQLI_ASSOC);
} catch (Throwable $e) {
    $error = $e;
}
ob_start();
?>
<article class="reporte" id="contenido-reporte">
    <header class="cabecera-reporte"><h2>La Prospera, S.A.</h2><p>Reporte para la Caja de Seguro Social</p><p>Segunda quincena de junio de 2026</p></header>
    <div class="tabla-responsive"><table class="tabla-datos"><thead><tr><th>Nombre</th><th>Cédula</th><th>Salario cotizable</th><th>CSS trabajador</th><th>Seguro Educativo</th><th>Total reportado</th></tr></thead><tbody>
    <?php $base=0; $css=0; $se=0; foreach ($filas as $f): $base+=(float)$f['base_cotizable']; $css+=(float)$f['seguro_social']; $se+=(float)$f['seguro_educativo']; ?>
        <tr><td><?= escapar($f['nombre_completo']) ?></td><td><?= escapar($f['cedula']) ?></td><td><?= formatoDinero($f['base_cotizable']) ?></td><td><?= formatoDinero($f['seguro_social']) ?></td><td><?= formatoDinero($f['seguro_educativo']) ?></td><td><?= formatoDinero((float)$f['seguro_social']+(float)$f['seguro_educativo']) ?></td></tr>
    <?php endforeach; ?>
    <?php if ($filas): ?><tr class="fila-total"><td colspan="2">Totales</td><td><?= formatoDinero($base) ?></td><td><?= formatoDinero($css) ?></td><td><?= formatoDinero($se) ?></td><td><?= formatoDinero($css+$se) ?></td></tr><?php endif; ?>
    </tbody></table></div>
    <p class="nota-reporte">Aportes patronales calculados por el sistema: CSS 13.25%, Seguro Educativo 1.50% y Riesgo Profesional 0.56%.</p>
</article>
<?php $contenido_reporte = ob_get_clean(); ?>
<?php require __DIR__ . '/../includes/header.php'; ?>
<div class="encabezado-pagina no-imprimir"><div><h1>Reporte CSS</h1><p>Salarios sujetos a cotización y cuotas del trabajador.</p></div></div>
<?php if ($error): mostrarErrorConexion($error); elseif (!$filas): ?><div class="mensaje mensaje-info">Procese las planillas antes de generar el reporte CSS.</div><?php else: echo $contenido_reporte; require __DIR__ . '/acciones.php'; endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>

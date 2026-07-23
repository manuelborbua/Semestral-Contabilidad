<?php
declare(strict_types=1);

$titulo_pagina = 'Reporte grupal';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/utilidades.php';
$filas = [];
$error = null;
try {
    $db = exigirConexion($conexion);
    $filas = $db->query('SELECT p.*, c.nombre_completo, c.cedula, c.cargo, c.salario_base FROM planillas p JOIN colaboradores c ON c.id=p.colaborador_id WHERE p.fecha_inicio="2026-06-16" AND p.fecha_fin="2026-06-30" ORDER BY c.nombre_completo')->fetch_all(MYSQLI_ASSOC);
} catch (Throwable $e) {
    $error = $e;
}
ob_start();
?>
<article class="reporte" id="contenido-reporte">
    <header class="cabecera-reporte"><h2>La Prospera, S.A.</h2><p>Reporte grupal — Segunda quincena de junio de 2026</p></header>
    <div class="tabla-responsive"><table class="tabla-datos tabla-reporte-amplia"><thead><tr><th>Nombre</th><th>Cédula</th><th>Cargo</th><th>Salario base</th><th>Otros ingresos</th><th>Bruto</th><th>Descuentos</th><th>Neto</th></tr></thead><tbody>
    <?php $totalBruto=0; $totalDescuentos=0; $totalNeto=0; foreach ($filas as $f): $otros=(float)$f['salario_bruto']-(float)$f['salario_quincenal']; $totalBruto+=(float)$f['salario_bruto']; $totalDescuentos+=(float)$f['total_descuentos']; $totalNeto+=(float)$f['salario_neto']; ?>
        <tr><td><?= escapar($f['nombre_completo']) ?></td><td><?= escapar($f['cedula']) ?></td><td><?= escapar($f['cargo']) ?></td><td><?= formatoDinero($f['salario_quincenal']) ?></td><td><?= formatoDinero($otros) ?></td><td><?= formatoDinero($f['salario_bruto']) ?></td><td><?= formatoDinero($f['total_descuentos']) ?></td><td><?= formatoDinero($f['salario_neto']) ?></td></tr>
    <?php endforeach; ?>
    <?php if ($filas): ?><tr class="fila-total"><td colspan="5">Totales</td><td><?= formatoDinero($totalBruto) ?></td><td><?= formatoDinero($totalDescuentos) ?></td><td><?= formatoDinero($totalNeto) ?></td></tr><?php endif; ?>
    </tbody></table></div>
</article>
<?php $contenido_reporte = ob_get_clean(); ?>
<?php require __DIR__ . '/../includes/header.php'; ?>
<div class="encabezado-pagina no-imprimir"><div><h1>Reporte grupal</h1><p>Resumen de los colaboradores del Grupo 5.</p></div></div>
<?php if ($error): mostrarErrorConexion($error); elseif (!$filas): ?><div class="mensaje mensaje-info">Procese los cuatro casos del Grupo 5 para generar este reporte.</div><?php else: echo $contenido_reporte; require __DIR__ . '/acciones.php'; endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>

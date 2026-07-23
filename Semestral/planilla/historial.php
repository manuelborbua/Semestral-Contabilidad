<?php
declare(strict_types=1);

$titulo_pagina = 'Historial de planillas';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/utilidades.php';
$filas = [];
$error = null;
try {
    $db = exigirConexion($conexion);
    $filas = $db->query('SELECT p.*, c.nombre_completo, c.cedula FROM planillas p JOIN colaboradores c ON c.id=p.colaborador_id ORDER BY p.fecha_fin DESC, c.nombre_completo')->fetch_all(MYSQLI_ASSOC);
} catch (Throwable $e) {
    $error = $e;
}
require __DIR__ . '/../includes/header.php';
?>
<div class="encabezado-pagina"><div><h1>Historial de planillas</h1><p>Cálculos guardados por periodo.</p></div></div>
<?php if ($error): mostrarErrorConexion($error); endif; ?>
<div class="tabla-responsive"><table class="tabla-datos"><thead><tr><th>Periodo</th><th>Colaborador</th><th>Bruto</th><th>Descuentos</th><th>Neto</th><th></th></tr></thead><tbody>
<?php if (!$filas): ?><tr><td colspan="6">Todavía no se han calculado planillas.</td></tr><?php endif; ?>
<?php foreach ($filas as $f): ?><tr><td><?= escapar($f['periodo']) ?></td><td><?= escapar($f['nombre_completo']) ?></td><td><?= formatoDinero($f['salario_bruto']) ?></td><td><?= formatoDinero($f['total_descuentos']) ?></td><td><?= formatoDinero($f['salario_neto']) ?></td><td><a href="../reportes/individual.php?id=<?= (int) $f['id'] ?>">Ver</a></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>

<?php
declare(strict_types=1);

$titulo_pagina = 'Calcular planilla';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/parametros.php';
require_once __DIR__ . '/../includes/utilidades.php';

$colaboradores = [];
$error = null;
try {
    $db = exigirConexion($conexion);
    $colaboradores = $db->query('SELECT id, nombre_completo, cedula, cargo, salario_base FROM colaboradores ORDER BY nombre_completo')->fetch_all(MYSQLI_ASSOC);
} catch (Throwable $e) {
    $error = $e;
}

require __DIR__ . '/../includes/header.php';
?>
<div class="encabezado-pagina">
    <div><h1>Calcular planilla</h1><p><?= escapar(PERIODO_PREDETERMINADO) ?></p></div>
    <form method="POST" action="procesar.php" class="no-imprimir">
        <button class="boton boton-dorado" name="procesar_grupo" value="1" type="submit">Procesar los 4 casos del Grupo 5</button>
    </form>
</div>
<?php if ($error): mostrarErrorConexion($error); endif; ?>
<div class="mensaje mensaje-info">
    La bonificación de B/.120 se aplica automáticamente. Los descuentos y dietas mensuales se dividen entre dos.
    El ISR solo se retiene cuando la renta neta gravable anual proyectada supera B/.11,000.
</div>
<form method="POST" action="procesar.php" class="formulario validar">
    <div class="campo">
        <label for="colaborador_id">Colaborador</label>
        <select id="colaborador_id" name="colaborador_id" required>
            <option value="">Seleccione</option>
            <?php foreach ($colaboradores as $c): ?>
                <option value="<?= (int) $c['id'] ?>"><?= escapar($c['nombre_completo']) ?> — <?= formatoDinero($c['salario_base']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="rejilla-formulario">
        <div class="campo"><label for="dieta_mensual">Dieta mensual</label><input type="number" min="0" step="0.01" id="dieta_mensual" name="dieta_mensual" value="0"></div>
        <div class="campo"><label for="cantidad_horas_extra">Horas extra diurnas</label><input type="number" min="0" step="0.25" id="cantidad_horas_extra" name="cantidad_horas_extra" value="0"></div>
        <div class="campo"><label for="ventas">Ventas del periodo</label><input type="number" min="0" step="0.01" id="ventas" name="ventas" value="0"></div>
        <div class="campo"><label for="porcentaje_comision">Comisión (%)</label><input type="number" min="0" step="0.001" id="porcentaje_comision" name="porcentaje_comision" value="0"></div>
        <div class="campo"><label for="otros_ingresos">Otros ingresos</label><input type="number" min="0" step="0.01" id="otros_ingresos" name="otros_ingresos" value="0"></div>
        <div class="campo"><label for="descuento_mensual">Descuento personal mensual</label><input type="number" min="0" step="0.01" id="descuento_mensual" name="descuento_mensual" value="0"></div>
    </div>
    <button class="boton boton-primario" type="submit">Calcular y guardar</button>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>

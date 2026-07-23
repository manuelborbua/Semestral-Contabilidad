<?php
declare(strict_types=1);

$titulo_pagina = 'Resultado del cálculo';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/utilidades.php';
require_once __DIR__ . '/funciones.php';
require_once __DIR__ . '/guardar.php';

$resultados = [];
$error = '';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new RuntimeException('Abra el formulario de cálculo antes de procesar una planilla.');
    }
    $db = exigirConexion($conexion);

    if (isset($_POST['procesar_grupo'])) {
        $casos = [
            'reparador de calle' => ['nombre' => 'Manuel Peña', 'dieta_mensual' => 600, 'descuento_mensual' => 250],
            'aseador' => ['nombre' => 'José Martínez', 'cantidad_horas_extra' => 5, 'descuento_mensual' => 50],
            'asistente de gerencia' => ['nombre' => 'Federico Montiel', 'cantidad_horas_extra' => 8, 'descuento_mensual' => 200],
            'vendedor supervisor' => ['nombre' => 'Estefanía Sousa Rincón', 'ventas' => 55000, 'porcentaje_comision' => 1.5, 'descuento_mensual' => 50],
        ];
        $personas = $db->query('SELECT * FROM colaboradores ORDER BY id')->fetch_all(MYSQLI_ASSOC);
        $porCargo = [];
        foreach ($personas as $persona) {
            $porCargo[strtolower(trim($persona['cargo']))] = $persona;
        }
        foreach ($casos as $cargo => $novedades) {
            $nombreEsperado = $novedades['nombre'];
            unset($novedades['nombre']);
            if (!isset($porCargo[$cargo])) {
                throw new RuntimeException('Falta registrar a ' . $nombreEsperado . '. Importe database/planilla.sql.');
            }
            $calculo = calcularPlanilla($porCargo[$cargo], $novedades);
            $idPlanilla = guardarPlanilla($db, (int) $porCargo[$cargo]['id'], $novedades, $calculo);
            $resultados[] = ['persona' => $porCargo[$cargo], 'calculo' => $calculo, 'id' => $idPlanilla];
        }
    } else {
        $id = (int) ($_POST['colaborador_id'] ?? 0);
        $stmt = $db->prepare('SELECT * FROM colaboradores WHERE id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $persona = $stmt->get_result()->fetch_assoc();
        if (!$persona) {
            throw new RuntimeException('Seleccione un colaborador válido.');
        }
        $novedades = [
            'dieta_mensual' => (float) ($_POST['dieta_mensual'] ?? 0),
            'cantidad_horas_extra' => (float) ($_POST['cantidad_horas_extra'] ?? 0),
            'ventas' => (float) ($_POST['ventas'] ?? 0),
            'porcentaje_comision' => (float) ($_POST['porcentaje_comision'] ?? 0),
            'otros_ingresos' => (float) ($_POST['otros_ingresos'] ?? 0),
            'descuento_mensual' => (float) ($_POST['descuento_mensual'] ?? 0),
        ];
        foreach ($novedades as $campo => $valor) {
            if (!is_finite($valor) || $valor < 0) {
                throw new InvalidArgumentException('Los valores del cálculo no pueden ser negativos.');
            }
        }
        if ($novedades['porcentaje_comision'] > 100) {
            throw new InvalidArgumentException('El porcentaje de comisión no puede superar el 100%.');
        }
        $calculo = calcularPlanilla($persona, $novedades);
        $idPlanilla = guardarPlanilla($db, $id, $novedades, $calculo);
        $resultados[] = ['persona' => $persona, 'calculo' => $calculo, 'id' => $idPlanilla];
    }
} catch (Throwable $e) {
    $error = $e->getMessage();
}

require __DIR__ . '/../includes/header.php';
?>
<div class="encabezado-pagina"><div><h1>Resultado del cálculo</h1><p><?= escapar(PERIODO_PREDETERMINADO) ?></p></div></div>
<?php if ($error): ?><div class="mensaje mensaje-error"><?= escapar($error) ?></div><?php endif; ?>
<?php foreach ($resultados as $r): $c = $r['calculo']; ?>
<section class="tarjeta resumen-calculo">
    <h2><?= escapar($r['persona']['nombre_completo']) ?></h2>
    <div class="rejilla-resumen">
        <span>Salario quincenal<strong><?= formatoDinero($c['salario_quincenal']) ?></strong></span>
        <span>Bonificación<strong><?= formatoDinero($c['bonificacion']) ?></strong></span>
        <span>Horas extra<strong><?= formatoDinero($c['horas_extras']) ?></strong></span>
        <span>Comisión<strong><?= formatoDinero($c['comision']) ?></strong></span>
        <span>Dieta<strong><?= formatoDinero($c['dieta']) ?></strong></span>
        <span>Salario bruto<strong><?= formatoDinero($c['salario_bruto']) ?></strong></span>
        <span>Seguro Social<strong>-<?= formatoDinero($c['seguro_social']) ?></strong></span>
        <span>Seguro Educativo<strong>-<?= formatoDinero($c['seguro_educativo']) ?></strong></span>
        <span>ISR<strong>-<?= formatoDinero($c['impuesto_renta']) ?></strong></span>
        <span>Otros descuentos<strong>-<?= formatoDinero($c['otros_descuentos']) ?></strong></span>
        <span class="resultado-neto">Salario neto<strong><?= formatoDinero($c['salario_neto']) ?></strong></span>
    </div>
    <a class="boton boton-secundario no-imprimir" href="../reportes/individual.php?id=<?= (int) $r['id'] ?>">Ver reporte individual</a>
</section>
<?php endforeach; ?>
<a class="boton boton-primario no-imprimir" href="historial.php">Ir al historial</a>
<?php require __DIR__ . '/../includes/footer.php'; ?>

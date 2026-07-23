<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/parametros.php';

function dinero(float $valor): float
{
    return round($valor + 0.0000001, 2);
}

function calcularSalarioQuincenal(float $salarioMensual): float
{
    return dinero($salarioMensual / 2);
}

function calcularHorasExtras(float $salarioMensual, float $horas): float
{
    if ($horas <= 0) {
        return 0.00;
    }

    return dinero(($salarioMensual / HORAS_MENSUALES) * $horas * RECARGO_EXTRA_DIURNA);
}

function calcularComision(float $ventas, float $porcentaje): float
{
    return dinero($ventas * ($porcentaje / 100));
}

/**
 * La guía indica que las dietas recurrentes cotizan únicamente en la parte
 * que exceda el 25% de un mes de salario.
 */
function calcularDietaCotizableQuincenal(float $salarioMensual, float $dietaMensual): float
{
    $excesoMensual = max(0, $dietaMensual - ($salarioMensual * 0.25));
    return dinero($excesoMensual / 2);
}

function calcularBaseCotizable(array $datos): float
{
    return dinero(
        $datos['salario_quincenal']
        + $datos['bonificacion']
        + $datos['horas_extras']
        + $datos['comision']
        + $datos['otros_ingresos']
        + $datos['dieta_cotizable']
    );
}

function calcularSeguroSocial(float $baseCotizable): float
{
    return dinero($baseCotizable * TASA_CSS_EMPLEADO);
}

function calcularSeguroEducativo(float $baseCotizable): float
{
    return dinero($baseCotizable * TASA_SE_EMPLEADO);
}

function calcularISR(
    float $salarioMensual,
    float $dietaMensual,
    array $ingresosVariablesPeriodo,
    string $tipoDeclaracion
): float
{
    /*
     * La tarifa se aplica sobre la renta neta gravable anual:
     * - salario regular: 12 meses + XIII mes;
     * - dieta recurrente: 12 meses;
     * - ingresos variables conocidos de esta quincena: una sola vez.
     *
     * No se multiplica por 26 la bonificación, comisión u hora extra de un
     * único periodo, porque eso supondría que se repiten todo el año.
     */
    $rentaAnual = ($salarioMensual * 13)
        + ($dietaMensual * 12)
        + array_sum($ingresosVariablesPeriodo);

    if (strtolower(trim($tipoDeclaracion)) === 'conjunta') {
        $rentaAnual = max(0, $rentaAnual - DEDUCCION_DECLARACION_CONJUNTA);
    }

    if ($rentaAnual <= 11000) {
        $impuestoAnual = 0;
    } elseif ($rentaAnual <= 50000) {
        $impuestoAnual = ($rentaAnual - 11000) * 0.15;
    } else {
        $impuestoAnual = 5850 + (($rentaAnual - 50000) * 0.25);
    }

    return dinero($impuestoAnual / 24);
}

function calcularSalarioNeto(float $salarioBruto, float $totalDescuentos): float
{
    return dinero($salarioBruto - $totalDescuentos);
}

function calcularPlanilla(array $colaborador, array $novedades): array
{
    $salarioMensual = (float) $colaborador['salario_base'];
    $dietaMensual = (float) ($novedades['dieta_mensual'] ?? 0);
    $descuentoMensual = (float) ($novedades['descuento_mensual'] ?? 0);

    $resultado = [
        'salario_quincenal' => calcularSalarioQuincenal($salarioMensual),
        'bonificacion' => dinero((float) ($novedades['bonificacion'] ?? BONIFICACION_QUINCENAL)),
        'horas_extras' => calcularHorasExtras($salarioMensual, (float) ($novedades['cantidad_horas_extra'] ?? 0)),
        'comision' => calcularComision(
            (float) ($novedades['ventas'] ?? 0),
            (float) ($novedades['porcentaje_comision'] ?? 0)
        ),
        'dieta' => dinero($dietaMensual / 2),
        'otros_ingresos' => dinero((float) ($novedades['otros_ingresos'] ?? 0)),
        'dieta_cotizable' => calcularDietaCotizableQuincenal($salarioMensual, $dietaMensual),
        'otros_descuentos' => dinero($descuentoMensual / 2),
    ];

    $resultado['salario_bruto'] = dinero(
        $resultado['salario_quincenal']
        + $resultado['bonificacion']
        + $resultado['horas_extras']
        + $resultado['comision']
        + $resultado['dieta']
        + $resultado['otros_ingresos']
    );
    $resultado['base_cotizable'] = calcularBaseCotizable($resultado);
    $resultado['seguro_social'] = calcularSeguroSocial($resultado['base_cotizable']);
    $resultado['seguro_educativo'] = calcularSeguroEducativo($resultado['base_cotizable']);
    $resultado['impuesto_renta'] = calcularISR(
        $salarioMensual,
        $dietaMensual,
        [
            $resultado['bonificacion'],
            $resultado['horas_extras'],
            $resultado['comision'],
            $resultado['otros_ingresos'],
        ],
        (string) ($colaborador['tipo_declaracion'] ?? 'Individual')
    );
    $resultado['total_descuentos'] = dinero(
        $resultado['seguro_social']
        + $resultado['seguro_educativo']
        + $resultado['impuesto_renta']
        + $resultado['otros_descuentos']
    );
    $resultado['salario_neto'] = calcularSalarioNeto(
        $resultado['salario_bruto'],
        $resultado['total_descuentos']
    );
    $resultado['css_patrono'] = dinero($resultado['base_cotizable'] * TASA_CSS_PATRONO);
    $resultado['se_patrono'] = dinero($resultado['base_cotizable'] * TASA_SE_PATRONO);
    $resultado['riesgo_profesional'] = dinero($resultado['base_cotizable'] * TASA_RIESGO_PROFESIONAL);

    return $resultado;
}

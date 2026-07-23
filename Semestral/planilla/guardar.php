<?php
declare(strict_types=1);

function guardarPlanilla(mysqli $db, int $colaboradorId, array $novedades, array $calculo): int
{
    $sql = 'INSERT INTO planillas (
        colaborador_id, periodo, fecha_inicio, fecha_fin, salario_quincenal, bonificacion,
        cantidad_horas_extra, horas_extras, ventas, porcentaje_comision, comision, dieta,
        otros_ingresos, salario_bruto, base_cotizable, seguro_social, seguro_educativo,
        impuesto_renta, otros_descuentos, total_descuentos, salario_neto, css_patrono,
        se_patrono, riesgo_profesional
    ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
    ON DUPLICATE KEY UPDATE
        periodo=VALUES(periodo), salario_quincenal=VALUES(salario_quincenal),
        bonificacion=VALUES(bonificacion), cantidad_horas_extra=VALUES(cantidad_horas_extra),
        horas_extras=VALUES(horas_extras), ventas=VALUES(ventas),
        porcentaje_comision=VALUES(porcentaje_comision), comision=VALUES(comision),
        dieta=VALUES(dieta), otros_ingresos=VALUES(otros_ingresos),
        salario_bruto=VALUES(salario_bruto), base_cotizable=VALUES(base_cotizable),
        seguro_social=VALUES(seguro_social), seguro_educativo=VALUES(seguro_educativo),
        impuesto_renta=VALUES(impuesto_renta), otros_descuentos=VALUES(otros_descuentos),
        total_descuentos=VALUES(total_descuentos), salario_neto=VALUES(salario_neto),
        css_patrono=VALUES(css_patrono), se_patrono=VALUES(se_patrono),
        riesgo_profesional=VALUES(riesgo_profesional), fecha_creacion=CURRENT_TIMESTAMP';

    $stmt = $db->prepare($sql);
    $periodo = PERIODO_PREDETERMINADO;
    $inicio = FECHA_INICIO_PERIODO;
    $fin = FECHA_FIN_PERIODO;
    $cantidadHoras = (float) ($novedades['cantidad_horas_extra'] ?? 0);
    $ventas = (float) ($novedades['ventas'] ?? 0);
    $porcentaje = (float) ($novedades['porcentaje_comision'] ?? 0);

    $stmt->bind_param(
        'isssdddddddddddddddddddd',
        $colaboradorId, $periodo, $inicio, $fin,
        $calculo['salario_quincenal'], $calculo['bonificacion'],
        $cantidadHoras, $calculo['horas_extras'], $ventas, $porcentaje,
        $calculo['comision'], $calculo['dieta'], $calculo['otros_ingresos'],
        $calculo['salario_bruto'], $calculo['base_cotizable'],
        $calculo['seguro_social'], $calculo['seguro_educativo'],
        $calculo['impuesto_renta'], $calculo['otros_descuentos'],
        $calculo['total_descuentos'], $calculo['salario_neto'],
        $calculo['css_patrono'], $calculo['se_patrono'], $calculo['riesgo_profesional']
    );
    $stmt->execute();

    if ($stmt->insert_id > 0) {
        return $stmt->insert_id;
    }

    $buscar = $db->prepare('SELECT id FROM planillas WHERE colaborador_id=? AND fecha_inicio=? AND fecha_fin=?');
    $buscar->bind_param('iss', $colaboradorId, $inicio, $fin);
    $buscar->execute();
    return (int) $buscar->get_result()->fetch_column();
}

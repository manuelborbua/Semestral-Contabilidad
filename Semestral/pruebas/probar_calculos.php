<?php
declare(strict_types=1);

require_once __DIR__ . '/../planilla/funciones.php';
$datos = require __DIR__ . '/datos_grupo5.php';
$esperados = require __DIR__ . '/resultados_esperados.php';

$personas = [
    'Manuel Peña' => ['salario_base' => 690, 'tipo_declaracion' => 'Conjunta'],
    'José Martínez' => ['salario_base' => 655.20, 'tipo_declaracion' => 'Individual'],
    'Federico Montiel' => ['salario_base' => 900, 'tipo_declaracion' => 'Conjunta'],
    'Estefanía Sousa Rincón' => ['salario_base' => 655.20, 'tipo_declaracion' => 'Individual'],
];

$fallos = 0;
foreach ($personas as $nombre => $persona) {
    $resultado = calcularPlanilla($persona, $datos[$nombre]);
    foreach ($esperados[$nombre] as $campo => $valor) {
        if (abs($resultado[$campo] - $valor) > 0.001) {
            fwrite(STDERR, "$nombre: $campo esperaba $valor y produjo {$resultado[$campo]}\n");
            $fallos++;
        }
    }
}

if ($fallos > 0) {
    exit(1);
}
echo "Todos los cálculos del Grupo 5 coinciden con los resultados esperados.\n";

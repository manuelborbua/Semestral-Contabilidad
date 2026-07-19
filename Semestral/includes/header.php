<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$raiz_proyecto = str_replace('\\', '/', __DIR__ . '/..');
$raiz_proyecto = str_replace('\\', '/', realpath($raiz_proyecto));
$document_root = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'], '/\\'));
$base_assets = str_replace($document_root, '', $raiz_proyecto);
if ($base_assets === '/') { $base_assets = ''; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Planilla Prospera<?php echo isset($titulo_pagina) ? ' — ' . htmlspecialchars($titulo_pagina) : ''; ?></title>
<link rel="stylesheet" href="<?php echo $base_assets; ?>/assets/css/estilos.css">
</head>
<body>

<?php include __DIR__ . '/navbar.php'; ?>

<main class="contenido-principal">

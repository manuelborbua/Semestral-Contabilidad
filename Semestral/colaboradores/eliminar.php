<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../includes/utilidades.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido');
}

try {
    $db = exigirConexion($conexion);
    $id = (int) ($_POST['id'] ?? 0);
    $stmt = $db->prepare('DELETE FROM colaboradores WHERE id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    header('Location: index.php?eliminado=1');
} catch (Throwable $e) {
    header('Location: index.php?error=' . urlencode('No se puede eliminar un colaborador con planillas guardadas.'));
}
exit;

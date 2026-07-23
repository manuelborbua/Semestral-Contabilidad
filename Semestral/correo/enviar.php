<?php
declare(strict_types=1);

$titulo_pagina = 'Enviar reporte por correo';
require_once __DIR__ . '/../includes/utilidades.php';

if (session_status() === PHP_SESSION_NONE) {
    $directorio_sesiones = __DIR__ . '/../storage/sessions';
    if (!is_dir($directorio_sesiones)) {
        mkdir($directorio_sesiones, 0775, true);
    }
    session_save_path($directorio_sesiones);
    session_start();
}

$mensaje_resultado = '';
$tipo_mensaje = '';
$contenido_reporte = $_POST['contenido_reporte'] ?? ($_SESSION['reporte_para_enviar'] ?? '');
$asunto_reporte = $_POST['asunto_reporte'] ?? ($_SESSION['asunto_para_enviar'] ?? 'Reporte de planilla — Planilla Prospera');

// Conserva el reporte entre la pantalla de confirmación y el envío.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['enviar_correo'])) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['reporte_para_enviar'] = $contenido_reporte;
    $_SESSION['asunto_para_enviar'] = $asunto_reporte;
}

function enviarConSmtp(string $destinatario, string $asunto, string $html): bool
{
    $autoload = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoload) || !getenv('SMTP_HOST')) {
        return false;
    }
    require_once $autoload;

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = getenv('SMTP_HOST');
    $mail->Port = (int) (getenv('SMTP_PORT') ?: 587);
    $mail->SMTPAuth = true;
    $mail->Username = getenv('SMTP_USER');
    $mail->Password = getenv('SMTP_PASSWORD');
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->CharSet = 'UTF-8';
    $remitente = getenv('MAIL_FROM') ?: 'no-responder@localhost';
    $mail->setFrom($remitente, 'Planilla Prospera');
    $mail->addAddress($destinatario);
    $mail->Subject = $asunto;
    $mail->isHTML(true);
    $mail->Body = $html;
    $mail->AltBody = trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $html)));
    return $mail->send();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_correo'])) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $contenido_reporte = $_SESSION['reporte_para_enviar'] ?? $contenido_reporte;
    $asunto_reporte = $_SESSION['asunto_para_enviar'] ?? $asunto_reporte;
    $destinatario = trim($_POST['correo_destino'] ?? '');

    if (!filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {
        $mensaje_resultado = 'El correo ingresado no es válido.';
        $tipo_mensaje = 'error';
    } elseif ($contenido_reporte === '') {
        $mensaje_resultado = 'No hay contenido de reporte para enviar.';
        $tipo_mensaje = 'error';
    } else {
        try {
            $enviado = enviarConSmtp($destinatario, $asunto_reporte, $contenido_reporte);
            if (!$enviado && !getenv('SMTP_HOST')) {
                $encabezados = "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\n";
                $encabezados .= 'From: Planilla Prospera <' . (getenv('MAIL_FROM') ?: 'no-responder@localhost') . ">\r\n";
                $enviado = mail($destinatario, $asunto_reporte, $contenido_reporte, $encabezados);
            }
            if (!$enviado) {
                throw new RuntimeException('Configure SMTP_HOST, SMTP_USER y SMTP_PASSWORD en el servidor.');
            }
            $mensaje_resultado = 'Reporte enviado correctamente a ' . $destinatario . '.';
            $tipo_mensaje = 'exito';
            unset($_SESSION['reporte_para_enviar'], $_SESSION['asunto_para_enviar']);
            $contenido_reporte = '';
        } catch (Throwable $e) {
            $mensaje_resultado = 'No se pudo enviar: ' . $e->getMessage();
            $tipo_mensaje = 'error';
        }
    }
}

require __DIR__ . '/../includes/header.php';
?>
<div class="encabezado-pagina"><div><h1>Enviar reporte por correo</h1><p>Entrega el reporte HTML mediante SMTP.</p></div></div>
<?php if ($mensaje_resultado): ?><div class="mensaje mensaje-<?= escapar($tipo_mensaje) ?>"><?= escapar($mensaje_resultado) ?></div><?php endif; ?>
<?php if ($contenido_reporte === ''): ?><div class="mensaje mensaje-info">Genere un reporte y use su botón “Enviar por correo”.</div><?php endif; ?>
<form method="POST" class="formulario validar formulario-correo">
    <div class="campo"><label for="correo_destino">Correo del destinatario</label><input type="email" id="correo_destino" name="correo_destino" required placeholder="profesora@utp.ac.pa"></div>
    <div class="campo"><label for="asunto_reporte">Asunto</label><input type="text" id="asunto_reporte" name="asunto_reporte" value="<?= escapar($asunto_reporte) ?>"></div>
    <button type="submit" name="enviar_correo" class="boton boton-dorado" <?= $contenido_reporte === '' ? 'disabled' : '' ?>>Enviar reporte</button>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>

# Planilla Prospera — Grupo 5

Sistema web del examen semestral de Contabilidad para calcular la
segunda quincena de junio de 2026.

## Ejecución local con XAMPP

1. Inicie MySQL desde el panel de XAMPP.
2. En phpMyAdmin, importe `Semestral/database/planilla.sql` usando
   codificación UTF-8.
3. Abra PowerShell en la carpeta del proyecto y ejecute:

```powershell
C:\xampp\php\php.exe -S 127.0.0.1:8080 -t Semestral
```

4. Abra `http://127.0.0.1:8080`.
5. Entre en **Calcular planilla** y pulse
   **Procesar los 4 casos del Grupo 5**.

La base incluye exclusivamente a Manuel Peña, José Martínez, Federico
Montiel y Estefanía Sousa Rincón.

## Correo

La pantalla de correo utiliza PHPMailer cuando se instalan las
dependencias de `composer.json` y se configuran las variables SMTP. En
un XAMPP con correo saliente configurado también puede usar `mail()` de
PHP. La configuración del proveedor de correo se realizará junto con
la etapa posterior de publicación.

## Validación

Con PHP disponible:

```sh
php Semestral/pruebas/probar_calculos.php
```

Los criterios y cifras esperadas están documentados en
`Semestral/documentacion/calculos_grupo5.md`.

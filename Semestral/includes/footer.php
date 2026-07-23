</main>

<?php
if (!isset($base_assets)) {
    $raiz_proyecto = str_replace('\\', '/', realpath(__DIR__ . '/..'));
    $document_root = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'], '/\\'));
    $base_assets = str_replace($document_root, '', $raiz_proyecto);
    if ($base_assets === '/') { $base_assets = ''; }
}
?>

<footer class="footer-app">
    <p>&copy; <?php echo date('Y'); ?> Daniela Anaya - Alberto Chen - Manuel Borbua - Arturo Rodríguez - Brian Cona / Grupo 1GS231</p>
</footer>

<script src="<?php echo $base_assets; ?>/assets/js/app.js"></script>
</body>
</html>

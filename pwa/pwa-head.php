<?php
// Tags PWA para o <head>. $pwaRaiz = caminho relativo até a raiz do projeto (ex.: '' ou '../').
$pwaRaiz = $pwaRaiz ?? '';
?>
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#0f9df7">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="Biblo">
<link rel="manifest" href="<?= $pwaRaiz ?>manifest.webmanifest">
<link rel="icon" type="image/png" sizes="192x192" href="<?= $pwaRaiz ?>pwa/icon-192.png">
<link rel="apple-touch-icon" href="<?= $pwaRaiz ?>pwa/apple-touch-icon.png">
<script>
if ('serviceWorker' in navigator) {
  window.addEventListener('load', function () {
    navigator.serviceWorker.register('<?= $pwaRaiz ?>sw.js').catch(function () {});
  });
}
</script>

<?php
// Script de diagnóstico WebP - ELIMINAR DESPUÉS DE USAR
echo "<h2>Diagnóstico WebP / GD</h2><pre>";

// 1. ¿Está cargada la extensión GD?
echo "GD cargado: " . (extension_loaded('gd') ? "✅ SÍ" : "❌ NO") . "\n";

// 2. ¿Existe imagewebp()?
echo "imagewebp() existe: " . (function_exists('imagewebp') ? "✅ SÍ" : "❌ NO") . "\n";

// 3. Información de GD
if (function_exists('gd_info')) {
    $info = gd_info();
    echo "\nInformación de GD:\n";
    foreach ($info as $key => $val) {
        echo "  $key: " . ($val === true ? "✅ SÍ" : ($val === false ? "❌ NO" : $val)) . "\n";
    }
}

// 4. ¿Puede escribir en Juguetes/?
$dir = __DIR__ . '/Juguetes/';
if (!is_dir($dir)) mkdir($dir, 0777, true);
echo "\nDirectorio Juguetes/ existe: " . (is_dir($dir) ? "✅ SÍ" : "❌ NO") . "\n";
echo "Directorio Juguetes/ escribible: " . (is_writable($dir) ? "✅ SÍ" : "❌ NO") . "\n";

// 5. Prueba de creación de imagen WebP
if (function_exists('imagewebp')) {
    $img = imagecreatetruecolor(10, 10);
    $testFile = $dir . 'test_webp_diagnostico.webp';
    $ok = imagewebp($img, $testFile, 80);
    imagedestroy($img);
    echo "Crear WebP de prueba: " . ($ok ? "✅ ÉXITO" : "❌ FALLÓ") . "\n";
    if ($ok && file_exists($testFile)) {
        echo "Archivo creado en: $testFile (" . filesize($testFile) . " bytes)\n";
        unlink($testFile); // limpiar
    }
}

echo "\nPHP versión: " . PHP_VERSION . "\n";
echo "</pre>";
?>

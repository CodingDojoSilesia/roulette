<?php
require __DIR__ . '/Obfuscator.php';

$filename = __DIR__ . '/api/index_orginal'; // A PHP filename (without .php) that you want to obfuscate
$sData = file_get_contents($filename . '.php');
$sData = str_replace(array('<?php', '<?', '?>'), '', $sData); // Strip PHP open/close tags
$sObfusationData = new Obfuscator($sData, 'Class/Code NAME');
file_put_contents($filename . '_obfuscated.php', '<?php ' . "\r\n" . $sObfusationData);

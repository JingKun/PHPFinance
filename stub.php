<?php
Phar::mapPhar('PHPFinance.phar');

spl_autoload_register(function ($className) {
	$classPath = 'phar://PHPFinance.phar/lib/'.substr(str_replace('\\', '/', $className).'.php', 11);

	@include($classPath);
});
__HALT_COMPILER(); ?>

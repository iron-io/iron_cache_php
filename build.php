<?php
/**
 * This script creates  .phar archive with all required dependencies.
 * Archive usage:
 * include("phar://iron_cache.phar");
 * or
 * include("phar://".dirname(__FILE__)."/iron_cache.phar");
 */
 
@unlink('iron_cache.phar');

$phar = new Phar('iron_cache.phar');

# Loader
$phar->setStub('<?php
Phar::mapPhar("iron_cache.phar");
if (!class_exists("IronCore")){
    require "phar://iron_cache.phar/IronCore.class.php";
}
require "phar://iron_cache.phar/IronCache.class.php";
__HALT_COMPILER(); ?>');

# Files
$phar->addFile('../iron_core_php/IronCore.class.php','IronCore.class.php');
$phar->addFile('IronCache.class.php');
$phar->addFile('LICENSE', 'LICENSE');

echo "\ndone - ".(round(filesize('iron_cache.phar')/1024,2))." KB\n";

# Verification
require "phar://iron_cache.phar";
$cache = new IronCache();

echo "Build finished successfully\n";
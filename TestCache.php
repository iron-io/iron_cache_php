<?php

include("../iron_core_php/IronCore.class.php");
include("IronCache.class.php");

$cache = new IronCache('config.ini');
$cache->ssl_verifypeer = false;
#$cache->debug_enabled = true;
$cache->setCacheName('cache #1');
$key = "key #1";

echo "Put item on cache:";
$res = $cache->put($key, 777);
print_r($res);

echo "\nGet item from cache:";
$item = $cache->get($key);
print_r($item);

echo "Increment item on cache:";
$res = $cache->increment($key, -222);
print_r($res);

echo "\nGet item from cache:";
$item = $cache->get($key);
print_r($item);

echo "\nRemoving item from cache:";
$res = $cache->delete($key);
print_r($res);

echo "\nGet item from cache:";
$item = $cache->get($key);
var_dump($item);

echo "\nEnd\n";
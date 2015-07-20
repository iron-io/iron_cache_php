<?php

require(dirname(__DIR__) . '/vendor/autoload.php');

$cache = new \IronCache\IronCache();
$cache->ssl_verifypeer = false;
#$cache->debug_enabled = true;
$cache->setCacheName('cache #4');


for ($i = 0; $i < 10; $i++) {
    $key = "key ##$i";

    echo "Put item on cache:\n";
    $res = $cache->put($key, 777);
    var_dump($res);

    echo "\nGet item from cache:\n";
    $item = $cache->get($key);
    var_dump($item);

    echo "Increment item on cache:\n";
    $res = $cache->increment($key, -222);
    var_dump($res);

    echo "\nGet item from cache:\n";
    $item = $cache->get($key);
    var_dump($item);

    echo "\nRemoving item from cache:\n";
    $res = $cache->delete($key);
    var_dump($res);

    echo "\nGet item from cache:\n";
    $item = $cache->get($key);
    var_dump($item);

    echo "\nClear cache:\n";
    $res = $cache->clear();
    var_dump($res);


    echo "----$i----\n";
}


echo "\n done";

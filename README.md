IronCache PHP Client
-------------

Getting Started
==============

## Get credentials
To start using iron_cache_php, you need to sign up and get an oauth token.

1. Go to http://iron.io/ and sign up.
2. Get an Oauth Token at http://hud.iron.io/tokens

## Install iron_cache_php

There are two ways to use iron_cache_php:

#### Using precompiled phar archive:

Copy `iron_cache.phar` to target directory and include it:

```php
<?php
require_once "phar://iron_cache.phar";
```

Please note, [phar](http://php.net/manual/en/book.phar.php) extension available by default only from php 5.3.0
For php 5.2 you should install phar manually or use second option.

#### Using classes directly

1. Copy `IronCache.class.php` to target directory
2. Grab `IronCore.class.php` [there](https://github.com/iron-io/iron_core_php) and copy to target directory
3. Include both of them:

```php
<?php
require_once "IronCore.class.php"
require_once "IronCache.class.php"
```

## Configure
Three ways to configure IronCache:

* Passing array with options:

```php
<?php
$cache = new IronCache(array(
    'token' => 'XXXXXXXXX',
    'project_id' => 'XXXXXXXXX'
));
```
* Passing ini file name which stores your configuration options. Rename sample_config.ini to config.ini and include your Iron.io credentials (`token` and `project_id`):

```php
<?php
$cache = new IronCache('config.ini');
```

* Automatic config search - pass zero arguments to constructor and library will try to find config file in following locations:

    * `iron.ini` in current directory
    * `iron.json` in current directory
    * `IRON_CACHE_TOKEN`, `IRON_CACHE_PROJECT_ID` and other environment variables
    * `IRON_TOKEN`, `IRON_PROJECT_ID` and other environment variables
    * `.iron.ini` in user's home directory
    * `.iron.json` in user's home directory

The Basics
=========

**Put** an item in the cache:

```php
<?php
$res = $cache->put("mykey", "hello world!");
```

**Get** an item from the cache:

```php
<?php
$item = $cache->get("mykey");
```

**Increment** an item value in the cache:

```php
<?php
$res = $cache->increment("mykey", 1);
```

**Delete** an item from the cache:
```php
<?php
$res = $cache->delete("mykey");
```

Cache Selection
===============
    
Select cache before interacting with items
* In constructor: `$cache = new IronCache('config.ini', 'my_cache');`
* By method: `$cache->setCacheName('my_cache');`
* Do it later when you need: `$cache->getItem('my_cache','my_key');`


Using IronCache as session store
===============


```php
<?php
$cache = new IronCache();
$cache->set_as_session_store();

# Use session as usual
session_start();
...

```

Troubleshooting
===============

### http error: 0

If you see  `Uncaught exception 'Http_Exception' with message 'http error: 0 | '`
it most likely caused by misconfigured cURL https sertificates.
There are two ways to fix this error:

1. Disable SSL sertificate verification - add this line after IronCache initialization: `$cache->ssl_verifypeer = false;`
2. Switch to http protocol - add this to configuration options: `protocol = http` and `port = 80`

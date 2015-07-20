<?php
/**
 * PHP client for IronCache
 *
 * @link https://github.com/iron-io/iron_cache_php
 * @link http://www.iron.io/products/cache
 * @link http://dev.iron.io/
 * @version 1.0.0
 * @package IronCache
 * @copyright Feel free to copy, steal, take credit for, or whatever you feel like doing with this code. ;)
 */

namespace IronCache;

use IronCore\IronCore;
use IronCore\HttpException;

/**
 * Class IronCache
 * @package IronCache
 */
class IronCache extends IronCore
{
    protected $client_version = '0.1.3';
    protected $client_name = 'iron_cache_php';
    protected $product_name = 'iron_cache';
    protected $default_values = array(
        'protocol' => 'https',
        'host' => 'cache-aws-us-east-1.iron.io',
        'port' => '443',
        'api_version' => '1',
    );

    protected $cache_name;

    public $session_expire_time = 172800; # 2 days

    /**
     * @param string|array $config
     *        Array of options or name of config file.
     * Fields in options array or in config:
     *
     * Required:
     * - token
     * - project_id
     * Optional:
     * - protocol
     * - host
     * - port
     * - api_version
     * @param string|null $cache_name set default cache name
     */
    public function __construct($config = null, $cache_name = null)
    {
        $this->getConfigData($config);
        $this->url = "{$this->protocol}://{$this->host}:{$this->port}/{$this->api_version}/";
        $this->setCacheName($cache_name);
    }

    /**
     * Switch active project
     *
     * @param string $project_id Project ID
     * @throws \InvalidArgumentException
     */
    public function setProjectId($project_id)
    {
        if (!empty($project_id)) {
            $this->project_id = $project_id;
        }
        if (empty($this->project_id)) {
            throw new \InvalidArgumentException("Please set project_id");
        }
    }

    /**
     * Set default cache name
     *
     * @param string $cache_name name of cache
     * @throws \InvalidArgumentException
     */
    public function setCacheName($cache_name)
    {
        if (!empty($cache_name)) {
            $this->cache_name = $cache_name;
        }

    }

    public function getCaches($page = 0)
    {
        $url = "projects/{$this->project_id}/caches";
        $params = array();
        if ($page > 0) {
            $params['page'] = $page;
        }
        $this->setJsonHeaders();

        return self::json_decode($this->apiCall(self::GET, $url, $params));
    }

    /**
     * Get information about cache.
     * Also returns cache size.
     *
     * @param string $cache
     * @return mixed
     */
    public function getCache($cache)
    {
        $cache = self::encodeCache($cache);
        $url = "projects/{$this->project_id}/caches/$cache";
        $this->setJsonHeaders();

        return self::json_decode($this->apiCall(self::GET, $url));
    }

    /**
     * Push a item on the cache at 'key'
     *
     * Examples:
     * <code>
     * $cache->putItem("test_cache", 'default', "Hello world");
     * </code>
     * <code>
     * $cache->putItem("test_cache", 'default', array(
     *   "value" => "Test Item",
     *   'expires_in' => 2*24*3600, # 2 days
     *   "replace" => true
     * ));
     * </code>
     *
     * @param string $cache Name of the cache.
     * @param string $key Item key.
     * @param array|string $item
     *
     * @return mixed
     */
    public function putItem($cache, $key, $item)
    {
        $cache = self::encodeCache($cache);
        $key = self::encodeKey($key);
        $itm = new IronCacheItem($item);
        $req = $itm->asArray();
        $url = "projects/{$this->project_id}/caches/$cache/items/$key";

        $this->setJsonHeaders();
        $res = $this->apiCall(self::PUT, $url, $req);

        return self::json_decode($res);
    }

    /**
     * Get item from cache by key
     *
     * @param string $cache Cache name
     * @param string $key Cache key
     * @return mixed|null single item or null
     * @throws HttpException
     */
    public function getItem($cache, $key)
    {
        $cache = self::encodeCache($cache);
        $key = self::encodeKey($key);
        $url = "projects/{$this->project_id}/caches/$cache/items/$key";

        $this->setJsonHeaders();
        try {
            $res = $this->apiCall(self::GET, $url);
        } catch (HttpException $e) {
            if ($e->getCode() == HttpException::NOT_FOUND) {
                return null;
            } else {
                throw $e;
            }
        }

        return self::json_decode($res);
    }

    public function deleteItem($cache, $key)
    {
        $cache = self::encodeCache($cache);
        $key = self::encodeKey($key);
        $url = "projects/{$this->project_id}/caches/$cache/items/$key";

        $this->setJsonHeaders();

        return self::json_decode($this->apiCall(self::DELETE, $url));
    }

    /**
     * Atomically increments the value for key by amount.
     * Can be used for both increment and decrement by passing a negative value.
     * The value must exist and must be an integer.
     * The number is treated as an unsigned 64-bit integer.
     * The usual overflow rules apply when adding, but subtracting from 0 always yields 0.
     *
     * @param string $cache
     * @param string $key
     * @param int $amount Change by this value
     * @return mixed|void
     */
    public function incrementItem($cache, $key, $amount = 1)
    {
        $cache = self::encodeCache($cache);
        $key = self::encodeKey($key);
        $url = "projects/{$this->project_id}/caches/$cache/items/$key/increment";
        $params = array(
            'amount' => $amount
        );
        $this->setJsonHeaders();

        return self::json_decode($this->apiCall(self::POST, $url, $params));
    }


    /**
     * Shortcut for getItem($cache, $key)
     * Please set $cache name before use by setCacheName() method
     *
     * @param string $key
     * @return mixed|null
     * @throws \InvalidArgumentException
     */
    public function get($key)
    {
        return $this->getItem($this->cache_name, $key);
    }

    /**
     * Shortcut for putItem($cache, $key, $item)
     * Please set $cache name before use by setCacheName() method
     *
     * @param string $key
     * @param array|string $item
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function put($key, $item)
    {
        return $this->putItem($this->cache_name, $key, $item);
    }

    /**
     * Shortcut for deleteItem($cache, $key)
     * Please set $cache name before use by setCacheName() method
     *
     * @param string $key
     * @return mixed|void
     * @throws \InvalidArgumentException
     */
    public function delete($key)
    {
        return $this->deleteItem($this->cache_name, $key);
    }

    /**
     * Shortcut for incrementItem($cache, $key, $amount)
     * Please set $cache name before use by setCacheName() method
     *
     * @param string $key
     * @param int $amount
     * @return mixed|void
     * @throws \InvalidArgumentException
     */
    public function increment($key, $amount = 1)
    {
        return $this->incrementItem($this->cache_name, $key, $amount);
    }

    /**
     * Clear a Cache
     * Delete all items in a cache. This cannot be undone.
     *
     * @param string|null $cache Cache name or null
     * @return mixed
     */
    public function clear($cache = null)
    {
        if ($cache === null) {
            $cache = $this->cache_name;
        }
        $cache = self::encodeCache($cache);
        $url = "projects/{$this->project_id}/caches/$cache/clear";
        $params = array();
        $this->setJsonHeaders();

        return self::json_decode($this->apiCall(self::POST, $url, $params));
    }


    public function session_open($savePath, $sessionName)
    {
        $this->setCacheName($sessionName);

        return true;
    }

    public function session_close()
    {
        return true;
    }

    public function session_read($id)
    {
        $item = $this->get($id);
        if ($item !== null) {
            return $item->value;
        } else {
            return null;
        }
    }

    public function session_write($id, $data)
    {
        $this->put($id, array(
            "value" => $data,
            "expires_in" => $this->session_expire_time
        ));

        return true;
    }

    public function session_destroy($id)
    {
        try {
            $this->delete($id);
        } catch (\Exception $e) {
            # ignore any exceptions
        }

        return true;
    }

    public function session_gc($maxlifetime)
    {
        # auto-expire by default, no need for gc
        return true;
    }

    /**
     * Set IronCache as session store handler
     *
     * @param null|integer $session_expire_time Expire time in seconds
     */
    public function set_as_session_store($session_expire_time = null)
    {
        if ($session_expire_time != null) {
            $this->session_expire_time = $session_expire_time;
        }
        session_set_save_handler(
            array($this, 'session_open'),
            array($this, 'session_close'),
            array($this, 'session_read'),
            array($this, 'session_write'),
            array($this, 'session_destroy'),
            array($this, 'session_gc')
        );
    }


    /* PRIVATE FUNCTIONS */

    protected static function encodeCache($cache)
    {
        if (empty($cache)) {
            throw new \InvalidArgumentException('Please set $cache variable');
        }

        return rawurlencode($cache);
    }

    protected static function encodeKey($key)
    {
        if (empty($key)) {
            throw new \InvalidArgumentException('Please set $key variable');
        }

        return rawurlencode($key);
    }


    protected function setJsonHeaders()
    {
        $this->setCommonHeaders();
    }

    protected function setPostHeaders()
    {
        $this->setCommonHeaders();
        $this->headers['Content-Type'] = 'multipart/form-data';
    }
}

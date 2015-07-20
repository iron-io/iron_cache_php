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

/**
 * Class IronCacheItem
 * @package IronCache
 */
class IronCacheItem
{
    protected $value;
    protected $expires_in;
    protected $replace;
    protected $add;

    const MAX_EXPIRES_IN = 2592000;

    /**
     * Create a new item.
     *
     * @param array|string $item
     *        An array of item properties or a string of the item value.
     * Fields in item array:
     * Required:
     * - value: string - The item data, as a string.
     * Optional:
     * - expires_in: integer - How long in seconds to keep the item on the cache before it is deleted.
     *                         Default is 604,800 seconds (7 days). Maximum is 2,592,000 seconds (30 days).
     * - replace: boolean - Will only work if key already exists.
     * - add:     boolean - Will only work if key does not exist.
     */
    public function __construct($item)
    {
        if (is_string($item) || is_integer($item)) {
            $this->setValue($item);
        } elseif (is_array($item)) {
            $this->setValue($item['value']);
            if (array_key_exists("replace", $item)) {
                $this->setReplace($item['replace']);
            }
            if (array_key_exists("add", $item)) {
                $this->setAdd($item['add']);
            }
            if (array_key_exists("expires_in", $item)) {
                $this->setExpiresIn($item['expires_in']);
            }
        }
    }

    public function setValue($value)
    {
        if ($value === null) {
            throw new \InvalidArgumentException("Please specify a value");
        } else {
            $this->value = $value;
        }
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setReplace($replace)
    {
        $this->replace = $replace;
    }

    public function getReplace()
    {
        return $this->replace;
    }

    public function setAdd($add)
    {
        $this->add = $add;
    }

    public function getAdd()
    {
        return $this->add;
    }

    public function setExpiresIn($expires_in)
    {
        if ($expires_in > self::MAX_EXPIRES_IN) {
            throw new \InvalidArgumentException("Expires In can't be greater than " . self::MAX_EXPIRES_IN . ".");
        } else {
            $this->expires_in = $expires_in;
        }
    }

    public function getExpiresIn()
    {
        return $this->expires_in;
    }

    public function asArray()
    {
        $array = array();
        $array['value'] = $this->getValue();
        if ($this->getExpiresIn() != null) {
            $array['expires_in'] = $this->getExpiresIn();
        }
        if ($this->getReplace() != null) {
            $array['replace'] = $this->getReplace();
        }
        if ($this->getAdd() != null) {
            $array['add'] = $this->getAdd();
        }

        return $array;
    }
}

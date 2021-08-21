<?php

namespace Cphne\PsrTests\Cache;

use Psr\Cache\CacheItemInterface;

class CacheItemPool implements \Psr\Cache\CacheItemPoolInterface
{

    protected array $deferredItems = [];

    protected \SplFileObject $resource;


    public function __construct()
    {
        $this->resource = new \SplFileObject("cache", "r+b");
    }

    /**
     * @inheritDoc
     */
    public function getItem(string $key): CacheItemInterface
    {
        $this->resource->rewind();
        $hit = false;
        while (!$hit && !$this->resource->eof() && ($buffer = $this->resource->fgets()) !== false) {
            if (str_starts_with($buffer, $key)) {
                $hit = true;
            }
        }
        if ($buffer !== false && !empty($buffer)) {
            $data = explode("---", $buffer);
            return $this->createItem($key, $hit, array_pop($data));
        }
        return $this->createItem($key, false, $hit);
    }

    /**
     * @inheritDoc
     */
    public function getItems(array $keys = []): iterable
    {
        $items = [];
        foreach ($keys as $key) {
            $items[$key] = $this->getItem($key);
        }
        return $items;
    }

    /**
     * @inheritDoc
     */
    public function hasItem(string $key): bool
    {
        $this->resource->rewind();
        $hit = false;
        while (!$hit && !$this->resource->eof() && ($buffer = $this->resource->fgets()) !== false) {
            if (str_starts_with($buffer, $key)) {
                $hit = true;
            }
        }
        return $hit;
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        return file_put_contents("cache", "") !== false;
    }

    /**
     * @inheritDoc
     */
    public function deleteItem(string $key): bool
    {
        $exists = $this->hasItem($key);
        if (!$exists) {
            return false;
        }
        $length = strlen($this->resource->current());
        $this->resource->seek($this->resource->key());
        $pad = str_pad("", $length - 1, "%");
        return $this->resource->fwrite($pad, $length) !== false;
    }

    /**
     * @inheritDoc
     */
    public function deleteItems(array $keys): bool
    {
        $successful = true;
        foreach ($keys as $key) {
            $successful = $this->deleteItem($key);
        }
        return $successful;
    }

    /**
     * @inheritDoc
     */
    public function save(CacheItemInterface $item): bool
    {
        $data = \Closure::bind(
            function () {
                return $this->data;
            },
            $item,
            CacheItem::class
        );
        $this->resource->seek($this->resource->getSize());
        return $this->resource->fwrite($item->getKey() . "---" . $data() . PHP_EOL) !== false;
    }

    /**
     * @inheritDoc
     */
    public function saveDeferred(CacheItemInterface $item): bool
    {
        $this->deferredItems[] = $item;
        return true;
    }

    /**
     * @inheritDoc
     */
    public function commit(): bool
    {
        $success = true;
        foreach ($this->deferredItems as $deferredItem) {
            $success = $this->save($deferredItem);
        }
        return $success;
    }


    protected function createItem($key, $hit, $value = null)
    {
        $closure = \Closure::bind(
            static function ($key, $value, $hit) {
                $item = new CacheItem();
                $item->key = $key;
                if (!is_null($value)) { // NULL is not correct, possible value so check isset
                    $item->data = $value;
                }
                $item->isHit = $hit;
                return $item;
            },
            null,
            CacheItem::class
        );
        return $closure($key, $value, $hit);
    }
}

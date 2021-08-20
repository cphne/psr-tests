<?php


namespace Cphne\PsrTests\Cache;


class CacheItem implements \Psr\Cache\CacheItemInterface
{

    private \DateTimeInterface $expirationDate;

    private string $data;
    private bool $isHit = false;
    private string $key;

    /**
     * @inheritDoc
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @inheritDoc
     */
    public function get(): mixed
    {
        if (!$this->isHit()) {
            return null;
        }
        return unserialize($this->data);
    }

    /**
     * @inheritDoc
     */
    public function isHit(): bool
    {
        return $this->isHit;
    }

    /**
     * @inheritDoc
     */
    public function set(mixed $value): static
    {
        $data = serialize($value);
        $this->data = $data;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function expiresAt(?\DateTimeInterface $expiration): static
    {
        $this->expirationDate = $expiration;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function expiresAfter(\DateInterval|int|null $time): static
    {
        if ($time instanceof \DateInterval) {
            $this->expirationDate = (new \DateTime())->add($time);
        } elseif (is_int($time)) {
            $this->expirationDate = (new \DateTime())->add(new \DateInterval("PS" . $time . "S"));
        } elseif (is_null($time)) {
            $this->expirationDate = null;
        }

        return $this;
    }

}

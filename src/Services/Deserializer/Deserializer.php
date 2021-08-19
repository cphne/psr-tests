<?php

namespace Cphne\PsrTests\Services\Deserializer;


/**
 * Class Deserializer.
 */
class Deserializer
{
    /**
     * @throws DeserializerException
     */
    public function json(?string $content, bool $throw = false): ?array
    {
        if (is_null($content)) {
            if ($throw) {
                throw new DeserializerException("Deserialisation failed. Can't deserialize null.");
            }

            return $content;
        }

        try {
            return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $jsonException) {
            if ($throw) {
                throw new DeserializerException('Deserialisation failed.', 500, $jsonException);
            }
        }

        return null;
    }

    public function url(?string $content): array
    {
        $parsedContent = [];
        parse_str($content, $parsedContent);

        return $parsedContent;
    }
}

<?php

namespace App;

use Intervention\Image\ImageManager as BaseImageManager;

class ImageManagerCompat extends BaseImageManager
{
    /**
     * Backwards-compatible factory similar to ImageManager::usingDriver
     */
    public static function usingDriver(string|object $driver, mixed ...$options): BaseImageManager
    {
        return new self(self::resolveDriver($driver, ...$options));
    }

    /**
     * Compatibility method `read` that maps to `decodePath` or `decode`.
     * Accepts a file path or binary data.
     */
    public function read(mixed $source)
    {
        if (is_string($source) && file_exists($source)) {
            return $this->decodePath($source);
        }

        return $this->decode($source);
    }
}

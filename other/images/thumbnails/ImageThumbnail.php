<?php

class ImageThumbnail
{
    private $filepath;

    const JPG = 'jpg';
    const PNG = 'png';


    /**
     * ImageThumbnail constructor.
     *
     * @param string $filepath
     */
    public function __construct(string $filepath)
    {
        $this->filepath = $filepath;
    }


    /**
     * @param string $filepath
     * @return static
     */
    public static function make(string $filepath): self
    {
        return new self($filepath);
    }


    /**
     * Get thumbnail filename
     *
     * @param int|null $width
     * @param int|null $height
     * @param int|null $quality
     * @param string|null $format
     * @return string|null
     */
    public function getThumbnail(?int $width = null, ?int $height = null, ?int $quality = 100, string $format = null): ?string
    {
        if ($this->filepath && file_exists($this->getDiskPath($this->filepath))) {

            $key = $this->getKey(['width' => $width, 'height' => $height, 'quality' => $quality, 'format' => $format, 'content' => md5(file_get_contents($this->getDiskPath($this->filepath)))]);
            $filepath = $this->getFilepath($this->filepath, $key, $format);

            // Check if thumbnail exists
            $imageIsExists = file_exists($this->getDiskPath($this->filepath));
            $thumbnailIsExists = file_exists($this->getDiskPath($filepath));

            // Create new thumbnail with provided parameters
            // If thumbnail is not exists on disk
            if ($imageIsExists === true && $thumbnailIsExists === false) {
                $this->createThumbnail($this->filepath, $filepath, $width, $height, $quality, $format);
            }

            // Get thumbnail filename
            if ($imageIsExists === true) {
                return sprintf('%s/%s', pathinfo($this->filepath)['dirname'], $this->getFilename($this->filepath, $key, $format));
            }

            return $this->filepath;
        }

        return null;
    }


    /**
     * Get thumbnail key
     *
     * @param array $parameters
     * @return string
     */
    private function getKey(array $parameters = []): string
    {
        ksort($parameters);
        $items = [];

        foreach ($parameters as $key => $value) $items[] = sprintf('%s:%s', $key, $value);

        return md5(implode(':', $items));
    }


    /**
     * Get thumbnail filepath
     *
     * @param string $filepath
     * @param string $key
     * @param string|null $format
     * @return string
     */
    private function getFilepath(string $filepath, string $key, string $format = null): string
    {
        $path = pathinfo($filepath);
        $dirname = $path['dirname'] ?? null;

        return $dirname
            ? implode(DIRECTORY_SEPARATOR, [$dirname, static::getFilename($filepath, $key, $format)])
            : $filepath;
    }


    /**
     * Get thumbnail filename
     *
     * @param string $filepath
     * @param string $key
     * @param string|null $format
     * @return string
     */
    private function getFilename(string $filepath, string $key, string $format = null): string
    {
        $path = pathinfo($filepath);
        $filename = $path['filename'] ?? null;
        $extension = $path['extension'] ?? null;

        return $filename && $extension
            ? sprintf('%s__%s.%s', $filename, $key, $format ?? $extension)
            : $filepath;
    }


    /**
     * Get disk path
     *
     * @param string $filepath
     * @return string
     */
    private function getDiskPath(string $filepath): string
    {
        return str_replace('/storage/', '/var/www/storage/', $filepath);
    }


    /**
     * Create new thumbnail
     *
     * @param string $imageFilepath
     * @param string $thumbnailFilepath
     * @param int|null $width
     * @param int|null $height
     * @param int|null $quality
     * @param string|null $format
     */
    private function createThumbnail(string $imageFilepath, string $thumbnailFilepath, ?int $width = null, ?int $height = null, ?int $quality = 100, string $format = null): void
    {
        if (is_file($this->getDiskPath($imageFilepath))) {
            try {

                $imagick = new Imagick($this->getDiskPath($imageFilepath));
                $imagick->scaleImage($width ?? 0, $height ?? 0);
                $imagick->setImageFormat($format ?? self::JPG);
                $imagick->setImageCompressionQuality($quality ?? 100);

                file_put_contents($this->getDiskPath($thumbnailFilepath), $imagick->getImageBlob());

            } catch (\Throwable $exception) {

                //
            }
        }
    }
}
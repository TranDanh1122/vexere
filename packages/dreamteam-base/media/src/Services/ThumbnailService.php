<?php

namespace DreamTeam\Media\Services;

use DreamTeam\Base\Facades\BaseHelper;
use DreamTeam\Media\Facades\RvMedia;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Encoders\AutoEncoder;

class ThumbnailService
{
    protected string $imagePath;

    protected float $thumbRate;

    protected int|string|null $thumbWidth;

    protected int|string|null $thumbHeight;

    protected string $destinationPath;

    protected int|null $xCoordinate;

    protected int|null $yCoordinate;

    protected string $fitPosition;

    protected string $fileName;

    public function __construct(protected UploadsManager $uploadManager)
    {
        $this->thumbRate = 0.75;
        $this->xCoordinate = null;
        $this->yCoordinate = null;
        $this->fitPosition = 'center';
    }

    public function setImage(string $imagePath): self
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    public function getImage(): string
    {
        return $this->imagePath;
    }

    public function setSize(int|string $width, int|string $height = 'auto'): self
    {
        $this->thumbWidth = $width;
        $this->thumbHeight = $height;

        if (! $height || $height == 'auto') {
            $this->thumbHeight = 0;
        } elseif ($height == 'rate') {
            $this->thumbHeight = (int)($this->thumbWidth * $this->thumbRate);
        }

        if (! $width || $width == 'auto') {
            $this->thumbWidth = 0;
        } elseif ($width == 'rate') {
            $this->thumbWidth = (int)($this->thumbHeight * $this->thumbRate);
        }

        return $this;
    }

    public function getSize(): array
    {
        return [$this->thumbWidth, $this->thumbHeight];
    }

    public function setDestinationPath(string $destinationPath): self
    {
        $this->destinationPath = $destinationPath;

        return $this;
    }

    public function setCoordinates(int $xCoordination, int $yCoordination): self
    {
        $this->xCoordinate = $xCoordination;
        $this->yCoordinate = $yCoordination;

        return $this;
    }

    public function getCoordinates(): array
    {
        return [$this->xCoordinate, $this->yCoordinate];
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function save(string $type = 'fit'): bool|string
    {
        $fileName = File::basename($this->imagePath);

        if ($this->fileName) {
            $fileName = $this->fileName;
        }

        $destinationPath = sprintf('%s/%s', trim($this->destinationPath, '/'), $fileName);

        $thumbImage = RvMedia::imageManager()->read($this->imagePath);

        if ($this->thumbWidth && ! $this->thumbHeight) {
            $type = 'width';
        } elseif ($this->thumbHeight && ! $this->thumbWidth) {
            $type = 'height';
        }

        switch ($type) {
            case 'width':
                if (! $this->thumbWidth) {
                    return $destinationPath;
                }

                $thumbImage->resize($this->thumbWidth);

                break;

            case 'height':
                if (! $this->thumbHeight) {
                    return $destinationPath;
                }

                $thumbImage->resize(null, $this->thumbHeight);

                break;

            case 'resize':
                if (! $this->thumbWidth || ! $this->thumbHeight) {
                    return $destinationPath;
                }

                $thumbImage->resize($this->thumbWidth, $this->thumbHeight);

                break;

            case 'crop':
                if (! $this->thumbWidth || ! $this->thumbHeight) {
                    return $destinationPath;
                }

                $thumbImage->crop($this->thumbWidth, $this->thumbHeight, $this->xCoordinate, $this->yCoordinate);

                break;

            case 'fit':
            default:
                if (! $this->thumbWidth || ! $this->thumbHeight) {
                    return $destinationPath;
                }

                $thumbImage->cover($this->thumbWidth, $this->thumbHeight, $this->fitPosition);

                break;
        }

        try {
            $this->uploadManager->saveFile($destinationPath, $thumbImage->encode(new AutoEncoder()));
        } catch (Exception $exception) {
            Log::error($exception);

            throw $exception;
        }

        return $destinationPath;
    }
}

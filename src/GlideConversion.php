<?php

namespace Spatie\Image;

use League\Glide\Server;
use League\Glide\ServerFactory;
use Spatie\Image\Exceptions\CouldNotConvert;

final class GlideConversion
{
    /** @var string */
    protected $inputImage;

    /** @var string */
    protected $imageDriver = 'gd';

    /** @var string */
    protected $conversionResult = null;

    public static function create(string $inputImage): self
    {
        return new static($inputImage);
    }

    public function __construct(string $inputImage)
    {
        $this->inputImage = $inputImage;
    }

    public function useImageDriver(string $imageDriver): self
    {
        $this->imageDriver = $imageDriver;

        return $this;
    }

    public function performManipulations(Manipulations $manipulations)
    {
        foreach ($manipulations->getManipulationSequence() as $manipulationGroup) {

            $inputFile = $this->conversionResult ?? $this->inputImage;

            $glideServer = $this->createGlideServer($inputFile);

            $this->conversionResult = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $glideServer->makeImage(
                    pathinfo($inputFile, PATHINFO_BASENAME),
                    $this->prepareManipulations($manipulationGroup)
                );
        }

        return $this;
    }

    protected function createGlideServer($inputFile): Server
    {
        return ServerFactory::create([
            'source' => dirname($inputFile),
            'cache' => sys_get_temp_dir(),
            'driver' => $this->imageDriver,
        ]);
    }

    public function save(string $outputFile)
    {
        if ($this->conversionResult == '') {
            copy($this->inputImage, $outputFile);

            return;
        }

        rename($this->conversionResult, $outputFile);
    }

    protected function prepareManipulations(array $manipulationGroup): array
    {
        $glideManipulations = [];

        foreach ($manipulationGroup as $name => $argument) {
            $glideManipulations[$this->convertToGlideParameter($name)] = $argument;
        }

        return $glideManipulations;
    }

    protected function convertToGlideParameter(string $manipulationName): string
    {
        $conversions = [
            'width' => 'w',
            'height' => 'h',
            'blur' => 'blur',
            'pixelate' => 'pixel',
            'crop' => 'fit',
            'orientation' => 'or',
            'fit' => 'fit',
            'devicePixelRatio' => 'dpr',
            'brightness' => 'bri',
            'contrast' => 'con',
            'gamma' => 'gam',
            'sharpen' => 'sharp',
            'filter' => 'filt',
            'background' => 'bg',
            'border' => 'border',
            'quality' => 'q',
            'format' => 'fm',
        ];

        if (!isset($conversions[$manipulationName])) {
            throw CouldNotConvert::unknownManipulation($manipulationName);
        }

        return $conversions[$manipulationName];
    }
}

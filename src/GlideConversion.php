<?php

namespace Spatie\Image;

use League\Glide\Server;
use League\Glide\ServerFactory;
use Spatie\Image\Exceptions\CouldNotConvert;

/** @private */
final class GlideConversion
{
    /** @var string */
    private $inputImage;

    /** @var string */
    private $imageDriver = 'gd';

    /** @var string */
    private $conversionResult = null;

    public static function create(string $inputImage): self
    {
        return new self($inputImage);
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

            $watermarkPath = $this->extractWatermarkPath($manipulationGroup);

            $glideServer = $this->createGlideServer($inputFile, $watermarkPath);

            $this->conversionResult = sys_get_temp_dir().DIRECTORY_SEPARATOR.$glideServer->makeImage(
                    pathinfo($inputFile, PATHINFO_BASENAME),
                    $this->prepareManipulations($manipulationGroup)
                );
        }

        return $this;
    }

    /**
     * Removes the watermark path from the manipulationGroup and returns it. This way it can be injected into the Glide
     * server as the `watermarks` path.
     *
     * @param $manipulationGroup
     *
     * @return null|string
     */
    private function extractWatermarkPath(&$manipulationGroup)
    {
        if (array_key_exists('watermark', $manipulationGroup)) {
            $watermarkPath = dirname($manipulationGroup['watermark']);

            $manipulationGroup['watermark'] = basename($manipulationGroup['watermark']);

            return $watermarkPath;
        }
    }

    private function createGlideServer($inputFile, ?string $watermarkPath = null): Server
    {
        $config = [
            'source' => dirname($inputFile),
            'cache' => sys_get_temp_dir(),
            'driver' => $this->imageDriver,
        ];

        if ($watermarkPath) {
            $config['watermarks'] = $watermarkPath;
        }

        return ServerFactory::create($config);
    }

    public function save(string $outputFile)
    {
        if ($this->conversionResult == '') {
            copy($this->inputImage, $outputFile);

            return;
        }

        rename($this->conversionResult, $outputFile);
    }

    private function prepareManipulations(array $manipulationGroup): array
    {
        $glideManipulations = [];

        foreach ($manipulationGroup as $name => $argument) {
            $glideManipulations[$this->convertToGlideParameter($name)] = $argument;
        }

        return $glideManipulations;
    }

    private function convertToGlideParameter(string $manipulationName): string
    {
        $conversions = [
            'width' => 'w',
            'height' => 'h',
            'blur' => 'blur',
            'pixelate' => 'pixel',
            'crop' => 'fit',
            'manualCrop' => 'crop',
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
            'watermark' => 'mark',
            'watermarkWidth' => 'markw',
            'watermarkHeight' => 'markh',
            'watermarkFit' => 'markfit',
            'watermarkPaddingX' => 'markx',
            'watermarkPaddingY' => 'marky',
            'watermarkPosition' => 'markpos',
            'watermarkOpacity' => 'markalpha',
        ];

        if (! isset($conversions[$manipulationName])) {
            throw CouldNotConvert::unknownManipulation($manipulationName);
        }

        return $conversions[$manipulationName];
    }
}

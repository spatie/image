<?php

namespace Spatie\Image;

use League\Glide\Server;
use League\Glide\ServerFactory;

final class GlideManipulator
{
    /** @var string */
    protected $inputImage;

    /** @var string */
    protected $imageDriver = 'imagick';

    /** @var string */
    protected $conversionResult = '';

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
        $manipulations = $this->prepareManipulations($manipulations);

        $glideServer = $this->createGlideServer();

        $sourceFileName = pathinfo($this->sourceFile, PATHINFO_BASENAME);

        $this->conversionResult = $glideServer->getCachePath() . $glideServer->makeImage(
                $sourceFileName,
                $manipulations
            );
    }

    protected function createGlideServer(): Server
    {
        return ServerFactory::create([
            'source' => dirname($this->inputImage),
            'cache' => sys_get_temp_dir(),
            'driver' => $this->driver,
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

    protected function prepareManipulations(Manipulations $manipulations): array
    {
        return array_map(function(array $manipulationsParameters) {
            $manipulationsParameters[0] = $this->convertToGlideParameter($manipulationsParameters[0]);

            return $manipulationsParameters;
        }, $manipulations->toArray());
    }

    protected function convertToGlideParameter(string $manipulationFunctionName): string {
        $conversions = [
            'width' => 'w',
            'height' => 'h',
            'blur' => 'b',
        ];

        if (! isset($conversions[$manipulationFunctionName])) {
            throw new Exception('Unknown');
        }

        return $conversions[$manipulationFunctionName];
    }
}
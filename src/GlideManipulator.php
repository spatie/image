<?php

namespace Spatie\Image;

use League\Glide\Server;
use League\Glide\ServerFactory;

final class GlideManipulator
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
        $glideServer = $this->createGlideServer();

        $inputImageFileName = pathinfo($this->inputImage, PATHINFO_BASENAME);


        $this->conversionResult = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $glideServer->makeImage(
                $this->conversionResult ?? $inputImageFileName,
                $this->prepareManipulations($manipulations)
            );

        return $this;
    }

    protected function createGlideServer(): Server
    {
        return ServerFactory::create([
            'source' => dirname($this->inputImage),
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

    protected function prepareManipulations(Manipulations $manipulations): array
    {
        return array_reduce($manipulations->toArray(), function(array $glideManipulations, array $manipulation) {
            $manipulationName = $this->convertToGlideParameter($manipulation[0]);

            $glideManipulations[$manipulationName] = $manipulation[1];

            return $glideManipulations;

        }, []);
    }

    protected function convertToGlideParameter(string $manipulationFunctionName): string {
        $conversions = [
            'width' => 'w',
            'height' => 'h',
            'blur' => 'blur',
        ];

        if (! isset($conversions[$manipulationFunctionName])) {
            throw new Exception('Unknown');
        }

        return $conversions[$manipulationFunctionName];
    }
}
<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Enums\Unit;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can insert watermark to image', function (
    ImageDriver $driver,
    array $watermarkArguments,
) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/watermark.png");

    $testImage = $driver->loadFile(getTestJpg());

    $testImage
        ->watermark(getTestFile('watermark.png'), \Spatie\Image\Enums\AlignPosition::BottomRight, ...$watermarkArguments)
        ->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers')->with([
    'padding 25,10px' => [[25, 10]],
    'padding 10,25px' => [[10, 25]],
    'padding 25,10%' => [[25, 10, Unit::Percent]],
    'padding 10,25%' => [[10, 25, Unit::Percent]],
    'width 100px' => [[0, 0, Unit::Pixel, 100]],
    'width 50%' => [[0, 0, Unit::Pixel, 50, Unit::Percent]],
    'width 0,height 50px' => [[0, 0, Unit::Pixel, 0, Unit::Pixel, 50]],
    'width 100px, height 50px' => [[0, 0, Unit::Pixel, 100, Unit::Pixel, 50]],
    'width 100px, height 50%' => [[0, 0, Unit::Pixel, 100, Unit::Pixel, 50, Unit::Percent]],
    'width 100px, height 50px, strech' => [[0, 0, Unit::Pixel, 100, Unit::Pixel, 50, Unit::Pixel, Fit::Stretch]],
    'width 100%, height 50%, strech' => [[0, 0, Unit::Pixel, 100, Unit::Percent, 50, Unit::Percent, Fit::Stretch]],
    'opacity 50%' => [[0, 0, Unit::Pixel, 100, Unit::Percent, 50, Unit::Percent, Fit::Contain, 50]],
    'opacity 25%' => [[0, 0, Unit::Pixel, 100, Unit::Percent, 50, Unit::Percent, Fit::Contain, 25]],
]);

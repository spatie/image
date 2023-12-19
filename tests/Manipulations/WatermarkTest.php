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
    [[25, 10]],
    [[10, 25]],
    [[25, 10, Unit::Percent]],
    [[10, 25, Unit::Percent]],
    [[0, 0, Unit::Pixel, 100]],
    [[0, 0, Unit::Pixel, 50, Unit::Percent]],
    [[0, 0, Unit::Pixel, 0, Unit::Pixel, 50]],
    [[0, 0, Unit::Pixel, 100, Unit::Pixel, 50]],
    [[0, 0, Unit::Pixel, 100, Unit::Pixel, 50, Unit::Percent]],
    [[0, 0, Unit::Pixel, 100, Unit::Pixel, 50, Unit::Pixel, Fit::Stretch]],
    [[0, 0, Unit::Pixel, 100, Unit::Percent, 50, Unit::Percent, Fit::Stretch]],
]);

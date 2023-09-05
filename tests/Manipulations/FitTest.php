<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Enums\Fit;

it('can contain an image in the given dimensions', function (
    ImageDriver $driver,
    array $fitDimensions,
    int $expectedWidth,
    int $expectedHeight,
) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/fit-contain.jpg");

    $driver->load(getTestJpg())->fit(Fit::Contain, ...$fitDimensions)->save($targetFile);

    expect($targetFile)->toBeFile();

    $savedImage = $driver->load($targetFile);
    expect($savedImage->getWidth())->toBe($expectedWidth);
    expect($savedImage->getHeight())->toBe($expectedHeight);

})->with('drivers')->with([
    [[100, 60], 73, 60],
    [[60, 100], 60, 50],
    [[200, 200], 200, 165],
]);

it('can fill an image in the given dimensions', function (
    ImageDriver $driver,
    array $fitDimensions,
    int $expectedWidth,
    int $expectedHeight,
) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/fit-fill.jpg");

    $driver->load(getTestJpg())->fit(Fit::Fill, ...$fitDimensions)->save($targetFile);

    $savedImage = $driver->load($targetFile);
    expect($savedImage->getWidth())->toBe($expectedWidth);
    expect($savedImage->getHeight())->toBe($expectedHeight);
})->with('drivers')->with([
    [[500, 500], 500, 500],
    [[250, 300], 250, 300],
    [[100, 100], 100, 100],
]);

it('can fill and stretch an image in the given dimensions', function (
    ImageDriver $driver,
    array $fitDimensions,
    int $expectedWidth,
    int $expectedHeight,
) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/fit-max.jpg");

    $driver->load(getTestJpg())->fit(Fit::Max, ...$fitDimensions)->save($targetFile);

    $savedImage = $driver->load($targetFile);
    expect($savedImage->getWidth())->toBe($expectedWidth);
    expect($savedImage->getHeight())->toBe($expectedHeight);
})->with('drivers')->with([
    [[100, 100], 100, 100],
    [[250, 300], 250, 300],
    [[500, 500], 500, 500],
]);

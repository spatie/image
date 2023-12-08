<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Enums\Fit;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can contain an image in the given dimensions', function (
    ImageDriver $driver,
    array $fitDimensions,
    int $expectedWidth,
    int $expectedHeight,
) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/fit-contain.png");

    $driver->loadFile(getTestJpg())->fit(Fit::Contain, ...$fitDimensions)->save($targetFile);

    expect($targetFile)->toBeFile();

    $savedImage = $driver->loadFile($targetFile);
    expect($savedImage->getWidth())->toBe($expectedWidth);
    expect($savedImage->getHeight())->toBe($expectedHeight);

    assertMatchesImageSnapshot($targetFile);
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
    $targetFile = $this->tempDir->path("{$driver->driverName()}/fit-fill.png");

    $driver->loadFile(getTestJpg())->fit(Fit::Fill, ...$fitDimensions)->save($targetFile);

    $savedImage = $driver->loadFile($targetFile);
    expect($savedImage->getWidth())->toBe($expectedWidth);
    expect($savedImage->getHeight())->toBe($expectedHeight);
    assertMatchesImageSnapshot($targetFile);
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
    $targetFile = $this->tempDir->path("{$driver->driverName()}/fit-max.png");

    $driver->loadFile(getTestJpg())->fit(Fit::Max, ...$fitDimensions)->save($targetFile);

    $savedImage = $driver->loadFile($targetFile);
    expect($savedImage->getWidth())->toBe($expectedWidth);
    expect($savedImage->getHeight())->toBe($expectedHeight);
    assertMatchesImageSnapshot($targetFile);
})->with('drivers')->with([
    [[100, 100], 100, 100],
    [[250, 300], 250, 300],
    [[500, 500], 500, 500],
]);

it('can stretch an image to the given dimensions', function (
    ImageDriver $driver,
    array $fitDimensions,
    int $expectedWidth,
    int $expectedHeight,
) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/fit-stretch.png");

    $driver->loadFile(getTestJpg())->fit(Fit::Stretch, ...$fitDimensions)->save($targetFile);

    $savedImage = $driver->loadFile($targetFile);
    expect($savedImage->getWidth())->toBe($expectedWidth);
    expect($savedImage->getHeight())->toBe($expectedHeight);
    assertMatchesImageSnapshot($targetFile);
})->with('drivers')->with([
    [[100, 100], 100, 100],
    [[250, 300], 250, 300],
    [[500, 500], 500, 500],
    [[100, 500], 100, 500],
]);

it('can fit and add a background', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/fit-background.png");

    $driver->loadFile(getTestJpg())
        ->fit(Fit::Fill, 2000, 100)
        //->background('ff5733')
        ->save($targetFile);

    assertMatchesImageSnapshot($targetFile);

    //$this->fail('TODO: bug background color is not set');
})->with('drivers');

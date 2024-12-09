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

it('can downsize an image maintaining its dimensions if not within the bounds', function (
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
    [[100, 100], 100, 82],
    [[250, 200], 243, 200],
    [[500, 500], 340, 280],
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
        ->fit(fit: Fit::FillMax, desiredWidth: 800, desiredHeight: 400, backgroundColor: '#0073ff')
        ->save($targetFile);

    assertMatchesImageSnapshot($targetFile);

})->with('drivers');

it('can do fit and crop', function (
    ImageDriver $driver,
    array $fitDimensions,
    int $expectedWidth,
    int $expectedHeight,
) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/fit-crop.png");

    $driver->loadFile(getTestJpg())->fit(Fit::Crop, ...$fitDimensions)->save($targetFile);

    $savedImage = $driver->loadFile($targetFile);
    expect($savedImage->getWidth())->toBe($expectedWidth);
    expect($savedImage->getHeight())->toBe($expectedHeight);
    assertMatchesImageSnapshot($targetFile);
})->with('drivers')->with([
    [[500, 500], 500, 500],
    [[250, 300], 250, 300],
    [[100, 100], 100, 100],
]);

it('can fit a PNG palette image with transparency and preserve the signature', function (
    ImageDriver $driver,
) {
    $fitDimensions = [438, 170];
    $expectedWidth = 438;
    $expectedHeight = 170;

    $targetFile = $this->tempDir->path("{$driver->driverName()}/fit-palette-signature.png");

    $driver->loadFile(getTestFile('palette-signature.png'))->fit(Fit::Max, ...$fitDimensions)->save($targetFile);

    $savedImage = $driver->loadFile($targetFile);

    expect($savedImage->getWidth())->toBe($expectedWidth);
    expect($savedImage->getHeight())->toBe($expectedHeight);
    assertMatchesImageSnapshot($targetFile);
})->with('drivers');

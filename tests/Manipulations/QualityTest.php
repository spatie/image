<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Image;

it('can set the quality of an image', function (ImageDriver $driver, string $format) {
    if ($format === 'avif' && ! avifIsSupported($driver->driverName())) {
        $this->markTestSkipped('avif is not supported on this system');

        return;
    }

    $lowQualityTargetFile = $this->tempDir->path("{$driver->driverName()}/quality10.{$format}");
    $driver->loadFile(getTestJpg())->quality(10)->save($lowQualityTargetFile);

    $mediumQualityTargetFile = $this->tempDir->path("{$driver->driverName()}/quality50.{$format}");
    $driver->loadFile(getTestJpg())->quality(50)->save($mediumQualityTargetFile);

    $highQualityTargetFile = $this->tempDir->path("{$driver->driverName()}/quality90.{$format}");
    $driver->loadFile(getTestJpg())->quality(90)->save($highQualityTargetFile);

    expect(filesize($lowQualityTargetFile))->toBeLessThan(filesize($mediumQualityTargetFile));

    expect(filesize($mediumQualityTargetFile))->toBeLessThan(filesize($highQualityTargetFile));
})->with('drivers')->with(['jpg', 'png']);

it('does not invert the quality of an avif image', function (ImageDriver $driver) {
    if (! avifIsSupported($driver->driverName())) {
        $this->markTestSkipped('avif is not supported on this system');

        return;
    }

    $lowQualityTargetFile = $this->tempDir->path("{$driver->driverName()}/inverted10.avif");
    $driver->loadFile(getTestJpg())->quality(10)->save($lowQualityTargetFile);

    $highQualityTargetFile = $this->tempDir->path("{$driver->driverName()}/inverted90.avif");
    $driver->loadFile(getTestJpg())->quality(90)->save($highQualityTargetFile);

    // A quality of 90 should never produce a smaller file than a quality of 10.
    expect(filesize($highQualityTargetFile))->toBeGreaterThan(filesize($lowQualityTargetFile));
})->with('drivers');

// Only covers the imagick driver: the gd and vips drivers do not pass the
// quality on to every format when encoding to base64.
it('applies the quality to a base64 encoded image', function (string $format) {
    $lowQuality = Image::useImageDriver('imagick')
        ->loadFile(getTestJpg())->quality(10)->base64($format, false);

    $highQuality = Image::useImageDriver('imagick')
        ->loadFile(getTestJpg())->quality(90)->base64($format, false);

    expect(strlen($lowQuality))->toBeLessThan(strlen($highQuality));
})->with(['jpeg', 'png']);

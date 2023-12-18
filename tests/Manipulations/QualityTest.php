<?php

use Spatie\Image\Drivers\ImageDriver;

it('can set the quality of a png', function (ImageDriver $driver) {
    $lowQualityTargetFile = $this->tempDir->path("{$driver->driverName()}/quality10.png");
    $driver->loadFile(getTestJpg())->quality(10)->save($lowQualityTargetFile);

    $mediumQualityTargetFile = $this->tempDir->path("{$driver->driverName()}/quality50.png");
    $driver->loadFile(getTestJpg())->quality(50)->save($mediumQualityTargetFile);

    $highQualityTargetFile = $this->tempDir->path("{$driver->driverName()}/quality90.png");
    $driver->loadFile(getTestJpg())->quality(90)->save($highQualityTargetFile);

    expect(filesize($lowQualityTargetFile))->toBeLessThan(filesize($mediumQualityTargetFile));

    expect(filesize($mediumQualityTargetFile))->toBeLessThan(filesize($highQualityTargetFile));
})->with('drivers');

it('can set the quality for different formats', function (ImageDriver $driver, string $format, int $quality) {
    if ($format === 'avif' && ! function_exists('imageavif')) {
        $this->markTestSkipped('avif is not supported on this system');
        return;
    }

    $targetFile = $this->tempDir->path("{$driver->driverName()}/quality.{$format}");

    $driver->loadFile(getTestJpg())->quality($quality)->save($targetFile);

    expect($targetFile)->toBeFile()->toHaveMime("image/$format");
})->with('drivers')->with(['jpeg', 'png', 'webp', 'avif'])->with([10, 50, 90]);

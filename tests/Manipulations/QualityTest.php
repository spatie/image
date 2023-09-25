<?php

use Spatie\Image\Drivers\ImageDriver;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can set the quality of a png', function (ImageDriver $driver) {
    $lowQualityTargetFile = $this->tempDir->path("{$driver->driverName()}/quality10.png");
    $driver->load(getTestJpg())->quality(10)->save($lowQualityTargetFile);

    $mediumQualityTargetFile = $this->tempDir->path("{$driver->driverName()}/quality50.png");
    $driver->load(getTestJpg())->quality(50)->save($mediumQualityTargetFile);

    $highQualityTargetFile = $this->tempDir->path("{$driver->driverName()}/quality90.png");
    $driver->load(getTestJpg())->quality(90)->save($highQualityTargetFile);

    expect(filesize($lowQualityTargetFile))->toBeLessThan(filesize($mediumQualityTargetFile));

    expect(filesize($mediumQualityTargetFile))->toBeLessThan(filesize($highQualityTargetFile));
})->with('drivers');

it('can set the quality for different formats', function (ImageDriver $driver, string $format, int $quality) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/quality.{$format}");

    $driver->load(getTestJpg())->quality($quality)->save($targetFile);

    expect($targetFile)->toBeFile();
})->with('drivers')->with(['jpg', 'png', 'webp'])->with([10, 50, 90]);

<?php

use Spatie\Image\Drivers\ImageDriver;

it('can set the quality of an image', function (ImageDriver $driver, string $format) {
    // Vips quality differences are not reliable for small test images
    if ($driver->driverName() === 'vips') {
        $this->markTestSkipped('Vips quality differences not reliable for small test images');

        return;
    }

    // Webp quality on GitHub CI produces inconsistent file sizes
    if ($format === 'webp' && isRunningOnGitHub()) {
        $this->markTestSkipped('Webp quality is unreliable on GitHub CI');

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
})->with('drivers')->with(['jpg', 'webp']);

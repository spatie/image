<?php

use Spatie\Image\Drivers\ImageDriver;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('sets the background on a png with a transparent background', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/background.png");

    $driver->loadFile(getTestFile('transparent-bg.png'))->background('#ff5733')->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers');

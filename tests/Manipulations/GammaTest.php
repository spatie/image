<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Drivers\ImageDriver;
use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can apply gamma to an image', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/gamma.png");

    $driver->load(getTestJpg())->gamma(4.8)->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers');

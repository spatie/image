<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Enums\FlipDirection;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can flip an image', function (ImageDriver $driver, FlipDirection $direction) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/{$direction->name}.png");
    $driver->loadFile(getTestJpg())->flip($direction)->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers')->with(FlipDirection::cases());

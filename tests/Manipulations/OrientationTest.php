<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Enums\Orientation;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can rotate an image', function (ImageDriver $driver, ?Orientation $orientation) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/orientation-{$orientation?->name}.png");

    $driver->loadFile(getTestFile('portrait.jpg'))->orientation($orientation)->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers')->with([
    null,
    Orientation::Rotate0,
    Orientation::Rotate90,
    Orientation::Rotate180,
    Orientation::Rotate270,
]);

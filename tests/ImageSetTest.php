<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\ImageSet;

it('can process a set of images', function (ImageDriver $driver) {
    $targetFile1 = $this->tempDir->path("{$driver->driverName()}/file1.png");
    $targetFile2 = $this->tempDir->path("{$driver->driverName()}/file2.png");

    ImageSet::load([
        getTestJpg(),
        getTestPhoto(),
    ])->blur(30)->save([
        $targetFile1,
        $targetFile2,
    ]);

    $this->assertFileExists($targetFile1);
    $this->assertFileExists($targetFile2);

})->with('drivers');

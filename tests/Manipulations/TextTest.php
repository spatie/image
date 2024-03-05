<?php

use Spatie\Image\Drivers\ImageDriver;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can insert text to image', function (
    ImageDriver $driver,
    array $textArguments,
) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/text.png");

    $testImage = $driver->loadFile(getTestJpg());

    $testImage
        ->text(
            'Hello there! This is some text',
            ...$textArguments,
        )
        ->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers')->with([
    'fontSize 30' => [[30, 'ffffff', 0, 50, 0, getTestSupportPath('testFiles/comic.ttf')]],
    'fontSize 50' => [[50, 'ffffff', 0, 50, 0, getTestSupportPath('testFiles/comic.ttf')]],
    'fontSize 100' => [[100, 'ffffff', 0, 50, 0, getTestSupportPath('testFiles/comic.ttf')]],
    'width 100' => [[30, 'ffffff', 0, 50, 0, getTestSupportPath('testFiles/comic.ttf'), 100]],
    'width 200' => [[30, 'ffffff', 0, 50, 0, getTestSupportPath('testFiles/comic.ttf'), 200]],
    'width 300' => [[30, 'ffffff', 0, 50, 0, getTestSupportPath('testFiles/comic.ttf'), 300]],
    'angle 10' => [[30, 'ffffff', 0, 50, 10, getTestSupportPath('testFiles/comic.ttf')]],
    'red' => [[30, 'ff0000', 0, 50, 0, getTestSupportPath('testFiles/comic.ttf')]],
    'blue' => [[30, '0000ff', 0, 50, 0, getTestSupportPath('testFiles/comic.ttf')]],
]);

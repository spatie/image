<?php

namespace Spatie\Image\Test;

use Spatie\Image\Exceptions\InvalidTemporaryDirectory;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;

it('can modify an image while setting temporary path', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())
        ->setTemporaryDirectory(__DIR__.'/temp_conf')
        ->manipulate(function (Manipulations $manipulations) {
            $manipulations
                ->blur(50);
        })
        ->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('will throw an error if tempdir corrupt', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())
        ->setTemporaryDirectory('/user/willmostprobablynotexistandbecreatable')
        ->manipulate(function (Manipulations $manipulations) {
            $manipulations
                ->blur(50);
        })
        ->save($targetFile);
})->throws(InvalidTemporaryDirectory::class);

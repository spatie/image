<?php

namespace Spatie\Image\Test;

use Imagick;
use Intervention\Image\ImageManagerStatic as InterventionImage;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;

it('can modify an image using manipulations', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())
        ->manipulate(function (Manipulations $manipulations) {
            $manipulations
                ->blur(50);
        })
        ->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('can modify an image using a direct manipulation call', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())
        ->width(5)
        ->width(500)
        ->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('will create a file in the format according to its extension', function () {
    $targetFile = $this->tempDir->path('conversion.png');
    Image::load(getTestJpg())->save($targetFile);
    assertImageType($targetFile, IMAGETYPE_PNG);

    $targetFile = $this->tempDir->path('conversion.gif');
    Image::load(getTestJpg())->save($targetFile);
    assertImageType($targetFile, IMAGETYPE_GIF);

    $targetFile = $this->tempDir->path('conversion.jpg');
    Image::load(getTestJpg())->save($targetFile);
    assertImageType($targetFile, IMAGETYPE_JPEG);

    $targetFile = $this->tempDir->path('conversion.pjpg');
    Image::load(getTestJpg())->save($targetFile);
    assertImageType($targetFile, IMAGETYPE_JPEG);

    if (function_exists('imagecreatefromwebp')) {
        $targetFile = $this->tempDir->path('conversion.webp');
        Image::load(getTestJpg())->save($targetFile);
        assertImageType($targetFile, IMAGETYPE_WEBP);
    }

    // only test avif format againts PHP 8.0
    if (PHP_MAJOR_VERSION === 8 && PHP_MINOR_VERSION === 0) {
        //test avif format with gd driver
        if (function_exists('imagecreatefromavif')) {
            $targetFile = $this->tempDir->path('conversion.avif');
            Image::load(getTestJpg())->save($targetFile);
            assertImageType($targetFile, IMAGETYPE_AVIF);
        }

        //test avif format with imagick
        if (! empty(Imagick::queryFormats('AVIF*'))) {
            $targetFile = $this->tempDir->path('conversion.avif');
            Image::load(getTestJpg())->useImageDriver('imagick')->save($targetFile);
            $image = new Imagick($targetFile);
            expect($image->getImageFormat())->toBe('AVIF');
        }
    }

    //test tiff format with imagick
    if (! empty(Imagick::queryFormats('TIFF*'))) {
        $targetFile = $this->tempDir->path('conversion.tiff');
        Image::load(getTestJpg())->useImageDriver('imagick')->save($targetFile);
        $image = new Imagick($targetFile);
        expect($image->getImageFormat())->toBe('TIFF');
    }
});

it('will not force the format according to the output extension when a format manipulation was already set', function () {
    $targetFile = $this->tempDir->path('conversion.gif');

    Image::load(getTestJpg())->format('jpg')->save($targetFile);

    assertImageType($targetFile, IMAGETYPE_JPEG);
});

it('can get the width and height of an image', function () {
    expect(Image::load(getTestJpg())->getWidth())->toBe(340);

    expect(Image::load(getTestJpg())->getHeight())->toBe(280);
});

it('the image driver is set on the intervention static manager', function () {
    $image = Image::load(getTestJpg());

    $image->useImageDriver('gd');

    expect(InterventionImage::getManager()->config['driver'] ?? null)->toBe('gd');

    $image->useImageDriver('imagick');

    expect(InterventionImage::getManager()->config['driver'] ?? null)->toBe('imagick');
});

it('can modify files with the same name if they are in different folders', function () {
    $firstTargetFile = $this->tempDir->path('first.jpg');
    $secondTargetFile = $this->tempDir->path('second.jpg');

    Image::load(getTestFile('test.jpg'))
        ->sepia()
        ->apply()
        ->crop(Manipulations::CROP_CENTER, 100, 100)
        ->save($firstTargetFile);

    Image::load(getTestFile('testdir/test.jpg'))
        ->sepia()
        ->apply()
        ->crop(Manipulations::CROP_CENTER, 100, 100)
        ->save($secondTargetFile);

    expect(file_get_contents($secondTargetFile))->not->toBe(file_get_contents($firstTargetFile));
});

<?php

use Jcupitt\Vips\Image as VipsImage;
use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Image;
use Spatie\Pixelmatch\Pixelmatch;

it('keeps the correct orientation based on Exif data', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/orientation.jpg");

    $original = $driver->loadFile(getTestFile('testOrientation.jpg'));

    expect($original->getWidth())->toEqual(340);
    expect($original->getHeight())->toEqual(280);

    $image = $original->save($targetFile);

    expect($image->getWidth())->toEqual(340);
    expect($image->getHeight())->toEqual(280);
})->with('drivers');

it('auto rotates all EXIF orientations to the same result', function (ImageDriver $driver) {
    $sourceFile = getTestFile('testOrientation.jpg');

    // Create a canonical image by auto-orienting the source with Imagick
    $canonical = new Imagick($sourceFile);
    $canonical->setImageOrientation($canonical->getImageOrientation());
    $canonical->autoOrient();

    $orientedFiles = [];

    foreach (range(1, 8) as $orientation) {
        // Apply the reverse transformation so autoRotate will produce the canonical result
        $img = clone $canonical;

        match ($orientation) {
            1 => null,
            2 => $img->flopImage(),
            3 => $img->rotateImage('#000', 180),
            4 => $img->rotateImage('#000', 180) && $img->flopImage(),
            5 => $img->rotateImage('#000', 90) && $img->flopImage(),
            6 => $img->rotateImage('#000', -90),
            7 => $img->rotateImage('#000', -90) && $img->flopImage(),
            8 => $img->rotateImage('#000', 90),
        };

        $img->setImageOrientation($orientation);

        $orientedFile = $this->tempDir->path("orientation-source-{$orientation}.jpg");
        $img->writeImage($orientedFile);
        $img->destroy();

        $orientedFiles[$orientation] = $orientedFile;
    }

    $canonical->destroy();

    // Save orientation 1 as the reference
    $referenceFile = $this->tempDir->path("{$driver->driverName()}/orientation-reference.png");
    $driver->loadFile($orientedFiles[1])->save($referenceFile);

    // Every other orientation should produce the same image after autoRotate
    foreach (range(2, 8) as $orientation) {
        $targetFile = $this->tempDir->path("{$driver->driverName()}/orientation-{$orientation}.png");

        $newDriver = $driver->driverName() === 'imagick'
            ? Image::useImageDriver('imagick')
            : Image::useImageDriver('gd');

        $newDriver->loadFile($orientedFiles[$orientation])->save($targetFile);

        expect($newDriver->getWidth())->toEqual(340, "Orientation {$orientation}: width mismatch");
        expect($newDriver->getHeight())->toEqual(280, "Orientation {$orientation}: height mismatch");
        expect(Pixelmatch::new($referenceFile, $targetFile)->matches())
            ->toBeTrue("Orientation {$orientation}: pixels don't match orientation 1");
    }
})->with('drivers');

it('auto rotates a large JPEG without an out of order read error', function (int $orientation, int $expectedWidth, int $expectedHeight) {
    $sourceImage = VipsImage::black(2000, 1500)
        ->add([120, 140, 160])
        ->cast('uchar')
        ->copy(['interpretation' => 'srgb']);

    $sourceImage->set('orientation', $orientation);

    $sourceFile = $this->tempDir->path("large-orientation-{$orientation}.jpg");
    $sourceImage->writeToFile($sourceFile);

    $targetFile = $this->tempDir->path("vips/large-orientation-{$orientation}.jpg");

    Image::useImageDriver('vips')->loadFile($sourceFile)->save($targetFile);

    [$width, $height] = getimagesize($targetFile);

    expect($width)->toEqual($expectedWidth);
    expect($height)->toEqual($expectedHeight);
})->with([
    'orientation 3' => [3, 2000, 1500],
    'orientation 5' => [5, 1500, 2000],
    'orientation 6' => [6, 1500, 2000],
    'orientation 7' => [7, 1500, 2000],
    'orientation 8' => [8, 1500, 2000],
]);

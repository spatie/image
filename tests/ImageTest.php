<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Enums\ColorFormat;
use Spatie\Image\Exceptions\CouldNotLoadImage;
use Spatie\Image\Exceptions\InvalidImageDriver;
use Spatie\Image\Image;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can load an image', function () {
    $image = Image::load(getTestJpg());

    expect($image)->toBeInstanceOf(ImageDriver::class);
});

it('will use imagick if it is available', function () {
    $image = Image::load(getTestJpg());

    expect($image->driverName())->toEqual('imagick');
});

it('can load an image with the specified driver', function () {
    $image = Image::useImageDriver(Spatie\Image\Enums\ImageDriver::Gd)->loadFile(getTestJpg());

    expect($image)->toBeInstanceOf(ImageDriver::class);
});

it('can use a custom driver', function () {
    $image = Image::useImageDriver(CustomDriver::class)->loadFile(getTestJpg());

    expect($image->driverName())->toEqual('custom');
});

it('it can load image from file', function () {
    $image = Image::load(getTestJpg());

    expect($image->getHeight())->toEqual(280);
});

it('will throw an exception when no file exists at the given path', function () {
    $invalidPath = getTestJpg().'non-existing';

    Image::load($invalidPath);
})->throws(CouldNotLoadImage::class);

it('will throw an exception when passing an invalid image driver name', function () {
    Image::useImageDriver('invalid')->load(getTestJpg());
})->throws(InvalidImageDriver::class);

it('can load a png that makes libpng emit a warning when warnings are converted to exceptions', function () {
    $handler = function (int $level, string $message, string $file, int $line): never {
        throw new ErrorException($message, 0, $level, $file, $line);
    };

    set_error_handler($handler);

    try {
        if (str_contains(gd_info()['GD Version'], 'bundled')) {
            expect(fn () => imagecreatefromstring(file_get_contents(getTestFile('libpng-warning.png'))))
                ->toThrow(ErrorException::class, 'iCCP');
        }

        $image = Image::useImageDriver('gd')->loadFile(getTestFile('libpng-warning.png'));
        $targetFile = $this->tempDir->path('gd/libpng-warning.png');

        $image->resize(4, 4)->save($targetFile);

        $savedImage = Image::useImageDriver('gd')->loadFile($targetFile);

        $currentHandler = set_error_handler(null);
        restore_error_handler();
    } finally {
        restore_error_handler();
    }

    expect($savedImage->getWidth())->toBe(4)
        ->and($savedImage->getHeight())->toBe(4)
        ->and($savedImage->pickColor(0, 0, ColorFormat::Array))->toBe([200, 30, 30, 1.0])
        ->and($currentHandler)->toBe($handler);
});

it('preserves gd failure diagnostics and restores the error handler', function (string $contents, string $reason) {
    $path = $this->tempDir->path('gd/invalid-image.png');
    file_put_contents($path, $contents);

    $handler = function (int $level, string $message, string $file, int $line): never {
        throw new ErrorException($message, 0, $level, $file, $line);
    };

    set_error_handler($handler);

    try {
        expect(fn () => Image::useImageDriver('gd')->loadFile($path))
            ->toThrow(function (CouldNotLoadImage $exception) use ($path, $reason) {
                expect($exception->getMessage())->toContain($path, $reason);
            });

        $currentHandler = set_error_handler(null);
        restore_error_handler();
    } finally {
        restore_error_handler();
    }

    expect($currentHandler)->toBe($handler);
})->with([
    'unrecognized data' => ['not an image', 'not in a recognized format'],
    'empty data' => ['', 'not in a recognized format'],
    'truncated png' => [fn () => substr(file_get_contents(getTestFile('libpng-warning.png')), 0, -20), 'Read Error: truncated data'],
]);

it('can resize a gif without losing frames when Imagick is used', function () {
    $driver = Image::useImageDriver('imagick');
    $image = $driver->loadFile(getTestGif());
    $targetFile = $this->tempDir->path("{$driver->driverName()}/resize.gif");
    $numberOfFrames = count($image->image());
    expect($image->getHeight())->toEqual(320);

    $image->width(200)->save($targetFile);

    $targetImage = $driver->loadFile($targetFile);
    expect(count($targetImage->image()))->toBe($numberOfFrames);
    expect($targetImage->getWidth())->toEqual(200);
});

it('can save without a path to overwrite the original file', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/save-without-path.jpg");

    copy(getTestJpg(), $targetFile);

    $driver->loadFile($targetFile)->greyscale()->save();

    [$expectedWidth, $expectedHeight] = getimagesize(getTestJpg());
    [$width, $height] = getimagesize($targetFile);

    expect($targetFile)->toBeFile();
    expect($targetFile)->toHaveMime('image/jpeg');
    expect($width)->toBe($expectedWidth);
    expect($height)->toBe($expectedHeight);
})->with('drivers');

it('works with transparent pngs', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/saving-transparent-png.png");

    $driver->loadFile(getTestFile('transparent.png'))->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers');

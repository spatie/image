<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;

it('can optimize an image', function (ImageDriver $driver) {
    $optimizedFile = $this->tempDir->path("{$driver->driverName()}/optimize.jpg");
    $controlFile = $this->tempDir->path("{$driver->driverName()}/no-optimize.jpg");

    $testFile = getTestJpg();

    $optimizerChain = (new OptimizerChain)
        ->addOptimizer(new Jpegoptim([
            '--strip-all',
            '--all-progressive',
            '-m85',
        ]));

    $driver->load($testFile)->optimize($optimizerChain)->save($optimizedFile);
    $driver->load($testFile)->save($controlFile);

    expect(filesize($optimizedFile))->toBeLessThan(filesize($controlFile));
})->with('drivers');

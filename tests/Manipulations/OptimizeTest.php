<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Image;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;
use Spatie\ImageOptimizer\Optimizers\Pngquant;

it('can optimize an image', function () {
    $targetFile = $this->tempDir->path('optimized.jpg');

    Image::load(getTestFile('test.jpg'))
        ->optimize()
        ->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('can optimize an image when using apply', function () {
    $targetFile = $this->tempDir->path('optimized.jpg');

    Image::load(getTestFile('test.jpg'))
        ->apply()
        ->optimize()
        ->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('can optimize an image with the given optimization options', function () {
    $targetFile = $this->tempDir->path('optimized.jpg');

    Image::load(getTestFile('test.jpg'))
        ->optimize([
            'optimizers' => [
                Jpegoptim::class => [
                    '--all-progressive',
                ],
            ],
        ])
        ->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('can optimize an image with options format backward compatibility', function () {
    $targetFile = $this->tempDir->path('optimized.jpg');

    Image::load(getTestFile('test.jpg'))
        ->optimize([Jpegoptim::class => [
            '--all-progressive',
        ]])
        ->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('can optimize an image using a provided optimizer chain', function () {
    $targetFile = $this->tempDir->path('optimized.jpg');

    Image::load(getTestFile('test.jpg'))
        ->setOptimizeChain(OptimizerChainFactory::create())
        ->optimize([
            'optimizers' => [
                Pngquant::class => [
                    '--force',
                ],
                Jpegoptim::class => [
                    '--all-progressive',
                ],
            ],
        ])
        ->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('can optimize an image specifying a desired timeout with a configuration array key', function () {
    $targetFile = $this->tempDir->path('optimized.jpg');

    Image::load(getTestFile('test.jpg'))
        ->setOptimizeChain(OptimizerChainFactory::create())
        ->optimize([
            'timeout' => 120,
            'optimizers' => [
                Jpegoptim::class => [
                    '--all-progressive',
                ],
            ],
        ])
        ->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('can optimize an image specifying a desired timeout with a method argument', function () {
    $targetFile = $this->tempDir->path('optimized.jpg');

    Image::load(getTestFile('test.jpg'))
        ->setOptimizeChain(OptimizerChainFactory::create())
        ->optimize([
            'optimizers' => [
                Jpegoptim::class => [
                    '--all-progressive',
                ],
            ],
        ], timeout: 120)
        ->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('can optimize an image with options format backward compatibility and timeout parameter', function () {
    $targetFile = $this->tempDir->path('optimized.jpg');

    Image::load(getTestFile('test.jpg'))
        ->optimize([Jpegoptim::class => [
            '--all-progressive',
        ]], timeout: 120)
        ->save($targetFile);

    expect($targetFile)->toBeFile();
});

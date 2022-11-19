<?php

namespace Spatie\Image\Test;

use Spatie\Image\Manipulations;

it('can be serialized', function () {
    $manipulations = (new Manipulations())
        ->width(100)
        ->height(100)
        ->apply()
        ->pixelate(50)
        ->blur(10);

    $unserializedManipulations = unserialize(serialize($manipulations));

    expect($unserializedManipulations->getManipulationSequence()->toArray())
        ->toBe($manipulations->getManipulationSequence()->toArray());
});

it('can be constructed with a sequence array', function () {
    $sequenceArray = [
        [
        'filter' => 'greyscale',
        'width' => '50',
        ],
        [
        'height' => '100',
        ],
    ];

    $manipulations = (new Manipulations($sequenceArray));

    expect($manipulations->getManipulationSequence()->toArray())->toBe($sequenceArray);
});

it('can be constructed with a single sequence', function () {
    $sequenceArray = [
        [
        'filter' => 'greyscale',
        'width' => '50',
        ],
    ];

    $manipulations = (new Manipulations($sequenceArray));

    expect($manipulations->getManipulationSequence()->toArray())->toBe($sequenceArray);
});

it('can return an array of manipulations', function () {
    $sequenceArray = [
        ['width' => '123'],
        ['manualCrop' => '20,10,10,10'],
    ];

    $manipulations = Manipulations::create()
        ->width(123)
        ->apply()
        ->manualCrop(20, 10, 10, 10);

    expect($manipulations->toArray())->toBe($sequenceArray);
});

it('can create from sequence array', function () {
    $sequenceArray = [
        ['width' => '123'],
        ['manualCrop' => '20,10,10,10'],
    ];

    $manipulations = Manipulations::create($sequenceArray);

    expect($manipulations->toArray())->toBe($sequenceArray);
});

it('can create from single sequence', function () {
    $sequence = [
        [
        'manualCrop' => '20,10,10,10',
        'width' => '123',
        ],
    ];

    $manipulations = Manipulations::create($sequence);

    expect($manipulations->toArray())->toBe($sequence);
});

it('can merge itself with another instance', function () {
    $manipulations1 = (new Manipulations())
        ->width(10)
        ->pixelate(10);

    $manipulations2 = (new Manipulations())
        ->width(20)
        ->height(10)
        ->blur(10);

    $mergedManipulations = $manipulations1->mergeManipulations($manipulations2);

    expect($mergedManipulations->getManipulationSequence()->toArray())->toBe([[
        'width' => '20',
        'pixelate' => '10',
        'height' => '10',
        'blur' => '10',
    ]]);
});

it('can determine that it is empty', function () {
    $manipulations = new Manipulations();

    expect($manipulations->isEmpty())->toBeTrue();

    $manipulations->width(100);

    expect($manipulations->isEmpty())->toBeFalse();
});

it('can get the arguments of a manipulation', function () {
    $manipulations = new Manipulations();

    expect($manipulations->getManipulationArgument('optimize'))->toBeNull();

    $manipulations->optimize();

    expect($manipulations->getManipulationArgument('optimize'))->toBe('[]');

    $manipulations = new Manipulations();

    $manipulations->optimize(['hide_errors' => true]);

    expect($manipulations->getManipulationArgument('optimize'))->toBe('{"hide_errors":true}');
});

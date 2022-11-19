<?php

namespace Spatie\Image\Test;

use Spatie\Image\ManipulationSequence;

it('can hold an empty sequence', function () {
    $manipulationSequence = new ManipulationSequence();

    expect($manipulationSequence->toArray())->toBe([]);
});

it('can hold a manipulation', function () {
    $manipulationSequence = new ManipulationSequence();

    $manipulationSequence->addManipulation('height', '100');

    expect($manipulationSequence->toArray())->toBe([
        [
            'height' => '100',
        ],
    ]);
});

it('can hold multiple manipulations', function () {
    $manipulationSequence = new ManipulationSequence();

    $manipulationSequence
        ->addManipulation('height', '100')
        ->addManipulation('width', '200');

    expect($manipulationSequence->toArray())->toBe([
        [
            'height' => '100',
            'width' => '200',
        ],
    ]);
});

it('will replace a manipulation if its applied multiple times', function () {
    $manipulationSequence = new ManipulationSequence();

    $manipulationSequence
        ->addManipulation('height', '100')
        ->addManipulation('width', '200')
        ->addManipulation('height', '300');

    expect($manipulationSequence->toArray())->toBe([
        [
            'height' => '300',
            'width' => '200',
        ],
    ]);
});

it('can start a new group', function () {
    $manipulationSequence = new ManipulationSequence();

    $manipulationSequence
        ->addManipulation('height', '100')
        ->addManipulation('width', '200')
        ->startNewGroup()
        ->addManipulation('height', '300');

    expect($manipulationSequence->toArray())->toBe([
        [
            'height' => '100',
            'width' => '200',
        ],
        [
            'height' => '300',
        ],
    ]);
});

it('can remove a manipulation', function () {
    $manipulationSequence = new ManipulationSequence();

    $manipulationSequence
        ->addManipulation('height', '100')
        ->addManipulation('width', '200')
        ->removeManipulation('height');

    expect($manipulationSequence->toArray())->toBe([
        [
            'width' => '200',
        ],
    ]);
});

it('can be iterated over', function () {
    $manipulationSequence = new ManipulationSequence();

    $manipulationSequence->addManipulation('height', '100');

    expect($manipulationSequence)->each->toBe([
        'height' => '100',
    ]);
});

it('will remove empty groups', function () {
    $manipulationSequence = new ManipulationSequence();

    $manipulationSequence
        ->addManipulation('height', '100')
        ->startNewGroup()
        ->addManipulation('width', '200')
        ->removeManipulation('height');

    expect($manipulationSequence->toArray())->toBe([
        [
            'width' => '200',
        ],
    ]);
});

it('can merge two sequences containing the same manipulation', function () {
    $manipulationSequence1 = (new ManipulationSequence())->addManipulation('height', '100');

    $manipulationSequence2 = (new ManipulationSequence())->addManipulation('height', '200');

    $manipulationSequence1->merge($manipulationSequence2);

    expect($manipulationSequence1->toArray())->toBe([
        [
            'height' => '200',
        ],
    ]);
});

it('can merge two sequences containing multiple manipulations', function () {
    $manipulationSequence1 = (new ManipulationSequence())
        ->addManipulation('width', '50')
        ->addManipulation('height', '100');

    $manipulationSequence2 = (new ManipulationSequence())
        ->addManipulation('height', '200')
        ->addManipulation('pixelate', '');

    $manipulationSequence1->merge($manipulationSequence2);

    expect($manipulationSequence1->toArray())->toBe([
        [
            'width' => '50',
            'height' => '200',
            'pixelate' => '',
        ],
    ]);
});

it('can merge two sequences containing multiple groups', function () {
    $manipulationSequence1 = (new ManipulationSequence())
        ->addManipulation('width', '50')
        ->addManipulation('height', '100')
        ->startNewGroup()
        ->addManipulation('width', '50')
        ->addManipulation('height', '100');

    $manipulationSequence2 = (new ManipulationSequence())
        ->addManipulation('height', '200')
        ->addManipulation('pixelate', '')
        ->startNewGroup()
        ->addManipulation('brightness', '200')
        ->addManipulation('format', 'png');

    $manipulationSequence1->merge($manipulationSequence2);

    expect($manipulationSequence1->toArray())->toBe([
        [
            'width' => '50',
            'height' => '100',
        ],
        [
            'width' => '50',
            'height' => '200',
            'pixelate' => '',
        ],
        [
            'brightness' => '200',
            'format' => 'png',
        ],
    ]);
});

it('is serializable', function () {
    $sequence = (new ManipulationSequence())
        ->addManipulation('width', '50')
        ->addManipulation('height', '100')
        ->startNewGroup()
        ->addManipulation('width', '50')
        ->addManipulation('height', '100');

    $unserializedSequence = unserialize(serialize($sequence));

    expect($unserializedSequence->toArray())->toBe($sequence->toArray());
});

it('can be constructed with a sequence array', function () {
    $sequenceArray = [
        [
        'greyscale' => '',
        'width' => '50',
        ],
        [
        'height' => '100',
        ],
    ];

    $sequence = (new ManipulationSequence($sequenceArray));

    expect($sequence->toArray())->toBe($sequenceArray);
});

it('does not return empty groups when iterating a merged sequence', function () {
    $sequenceArray = [
        [
        'width' => '100',
        'height' => '100',
        ],
    ];

    $sequence1 = new ManipulationSequence();
    $sequence2 = new ManipulationSequence($sequenceArray);

    $mergedSequence = $sequence1->merge($sequence2);

    expect($mergedSequence)->toHaveCount(1)
        ->and(expect($mergedSequence)->each->toBe($sequenceArray[0]));
});

it('can determine that the sequence is empty', function () {
    $sequence = new ManipulationSequence();

    expect($sequence->isEmpty())->toBeTrue();

    $sequence->addManipulation('width', '50');

    expect($sequence->isEmpty())->toBeFalse();
});

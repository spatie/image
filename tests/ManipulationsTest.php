<?php

namespace Spatie\Image\Test;

use PHPUnit\Framework\TestCase;
use Spatie\Image\Manipulations;

class ManipulationsTest extends TestCase
{
    /** @test */
    public function it_can_be_serialized()
    {
        $manipulations = (new Manipulations())
            ->width(100)
            ->height(100)
            ->apply()
            ->pixelate(50)
            ->blur(10);

        $unserializedManipulations = unserialize(serialize($manipulations));

        $this->assertEquals(
            $manipulations->getManipulationSequence()->toArray(),
            $unserializedManipulations->getManipulationSequence()->toArray()
        );
    }

    /** @test */
    public function it_can_be_constructed_with_a_sequence_array()
    {
        $sequenceArray = [
            [
                'filter' => 'greyscale',
                'width' => 50,
            ],
            [
                'height' => 100,
            ],
        ];

        $manipulations = (new Manipulations($sequenceArray));

        $this->assertEquals($sequenceArray, $manipulations->getManipulationSequence()->toArray());
    }

    /** @test */
    public function it_can_be_constructed_with_a_single_sequence()
    {
        $sequenceArray = [
            [
                'filter' => 'greyscale',
                'width' => 50,
            ],
        ];

        $manipulations = (new Manipulations($sequenceArray));

        $this->assertEquals($sequenceArray, $manipulations->getManipulationSequence()->toArray());
    }

    /** @test */
    public function it_can_return_an_array_of_manipulations()
    {
        $sequenceArray = [
            ['width' => '123'],
            ['manualCrop' => '20,10,10,10'],
        ];

        $manipulations = Manipulations::create()
            ->width(123)
            ->apply()
            ->manualCrop(20, 10, 10, 10);

        $this->assertEquals($sequenceArray, $manipulations->toArray());
    }

    /** @test */
    public function it_can_create_from_sequence_array()
    {
        $sequenceArray = [
            ['width' => '123'],
            ['manualCrop' => '20,10,10,10'],
        ];

        $manipulations = Manipulations::create($sequenceArray);

        $this->assertEquals($sequenceArray, $manipulations->toArray());
    }

    /** @test */
    public function it_can_create_from_single_sequence()
    {
        $sequence = [
            [
                'manualCrop' => '20,10,10,10',
                'width' => '123',
            ],
        ];

        $manipulations = Manipulations::create($sequence);

        $this->assertEquals($sequence, $manipulations->toArray());
    }

    /** @test */
    public function it_can_merge_itself_with_another_instance()
    {
        $manipulations1 = (new Manipulations())
            ->width(10)
            ->pixelate(10);

        $manipulations2 = (new Manipulations())
            ->width(20)
            ->height(10)
            ->blur(10);

        $mergedManipulations = $manipulations1->mergeManipulations($manipulations2);

        $this->assertEquals([[
            'width' => 20,
            'pixelate' => 10,
            'height' => 10,
            'blur' => 10,
        ]], $mergedManipulations->getManipulationSequence()->toArray());
    }

    /** @test */
    public function it_can_determine_that_it_is_empty()
    {
        $manipulations = new Manipulations();

        $this->assertTrue($manipulations->isEmpty());

        $manipulations->width(100);

        $this->assertFalse($manipulations->isEmpty());
    }

    /** @test */
    public function it_can_get_the_arguments_of_a_manipulation()
    {
        $manipulations = new Manipulations();

        $this->assertNull($manipulations->getManipulationArgument('optimize'));

        $manipulations->optimize();

        $this->assertEquals('[]', $manipulations->getManipulationArgument('optimize'));

        $manipulations = new Manipulations();

        $manipulations->optimize(['hide_errors' => true]);

        $this->assertEquals('{"hide_errors":true}', $manipulations->getManipulationArgument('optimize'));
    }
}

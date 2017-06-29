<?php

namespace Spatie\Image\Test;

use Spatie\Image\ManipulationSequence;

class ManipulationSequenceTest extends TestCase
{
    /** @test */
    public function it_can_hold_an_empty_sequence()
    {
        $manipulationSequence = new ManipulationSequence();

        $this->assertEquals([], $manipulationSequence->toArray());
    }

    /** @test */
    public function it_can_hold_a_manipulation()
    {
        $manipulationSequence = new ManipulationSequence();

        $manipulationSequence->addManipulation('height', 100);

        $this->assertEquals([
            [
                'height' => 100,
            ],
        ], $manipulationSequence->toArray());
    }

    /** @test */
    public function it_can_hold_multiple_manipulations()
    {
        $manipulationSequence = new ManipulationSequence();

        $manipulationSequence
            ->addManipulation('height', 100)
            ->addManipulation('width', 200);

        $this->assertEquals([
            [
                'height' => 100,
                'width' => 200,
            ],
        ], $manipulationSequence->toArray());
    }

    /** @test */
    public function it_will_replace_a_manipulation_if_its_applied_multiple_times()
    {
        $manipulationSequence = new ManipulationSequence();

        $manipulationSequence
            ->addManipulation('height', 100)
            ->addManipulation('width', 200)
            ->addManipulation('height', 300);

        $this->assertEquals([
            [
                'height' => 300,
                'width' => 200,
            ],
        ], $manipulationSequence->toArray());
    }

    /** @test */
    public function it_can_start_a_new_group()
    {
        $manipulationSequence = new ManipulationSequence();

        $manipulationSequence
            ->addManipulation('height', 100)
            ->addManipulation('width', 200)
            ->startNewGroup()
            ->addManipulation('height', 300);

        $this->assertEquals([
            [
                'height' => 100,
                'width' => 200,
            ],
            [
                'height' => 300,
            ],
        ], $manipulationSequence->toArray());
    }

    /** @test */
    public function it_can_remove_a_manipulation()
    {
        $manipulationSequence = new ManipulationSequence();

        $manipulationSequence
            ->addManipulation('height', 100)
            ->addManipulation('width', 200)
            ->removeManipulation('height');

        $this->assertEquals([
            [
                'width' => 200,
            ],
        ], $manipulationSequence->toArray());
    }

    /** @test */
    public function it_can_be_iterated_over()
    {
        $manipulationSequence = new ManipulationSequence();

        $manipulationSequence->addManipulation('height', 100);

        foreach ($manipulationSequence as $manipulationSet) {
            $this->assertEquals([
                'height' => 100,
            ], $manipulationSet);
        }
    }

    /** @test */
    public function it_will_remove_empty_groups()
    {
        $manipulationSequence = new ManipulationSequence();

        $manipulationSequence
            ->addManipulation('height', 100)
            ->startNewGroup()
            ->addManipulation('width', 200)
            ->removeManipulation('height');

        $this->assertEquals([
            [
                'width' => 200,
            ],
        ], $manipulationSequence->toArray());
    }

    /** @test */
    public function it_can_merge_two_sequences_containing_the_same_manipulation()
    {
        $manipulationSequence1 = (new ManipulationSequence())->addManipulation('height', 100);

        $manipulationSequence2 = (new ManipulationSequence())->addManipulation('height', 200);

        $manipulationSequence1->merge($manipulationSequence2);

        $this->assertEquals([
            [
                'height' => 200,
            ],
        ], $manipulationSequence1->toArray());
    }

    /** @test */
    public function it_can_merge_two_sequences_containing_multiple_manipulations()
    {
        $manipulationSequence1 = (new ManipulationSequence())
            ->addManipulation('width', 50)
            ->addManipulation('height', 100);

        $manipulationSequence2 = (new ManipulationSequence())
            ->addManipulation('height', 200)
            ->addManipulation('pixelate', '');

        $manipulationSequence1->merge($manipulationSequence2);

        $this->assertEquals([
            [
                'width' => '50',
                'height' => 200,
                'pixelate' => '',
            ],
        ], $manipulationSequence1->toArray());
    }

    /** @test */
    public function it_can_merge_two_sequences_containing_multiple_groups()
    {
        $manipulationSequence1 = (new ManipulationSequence())
            ->addManipulation('width', 50)
            ->addManipulation('height', 100)
            ->startNewGroup()
            ->addManipulation('width', 50)
            ->addManipulation('height', 100);

        $manipulationSequence2 = (new ManipulationSequence())
            ->addManipulation('height', 200)
            ->addManipulation('pixelate', '')
            ->startNewGroup()
            ->addManipulation('brightness', 200)
            ->addManipulation('format', 'png');

        $manipulationSequence1->merge($manipulationSequence2);

        $this->assertEquals([
            [
                'width' => 50,
                'height' => 100,
            ],
            [
                'width' => '50',
                'height' => 200,
                'pixelate' => '',
            ],
            [
                'brightness' => 200,
                'format' => 'png',
            ],
        ], $manipulationSequence1->toArray());
    }

    /** @test */
    public function it_is_serializable()
    {
        $sequence = (new ManipulationSequence())
            ->addManipulation('width', 50)
            ->addManipulation('height', 100)
            ->startNewGroup()
            ->addManipulation('width', 50)
            ->addManipulation('height', 100);

        $unserializedSequence = unserialize(serialize($sequence));

        $this->assertEquals($sequence->toArray(), $unserializedSequence->toArray());
    }

    /** @test */
    public function it_can_be_constructed_with_a_sequence_array()
    {
        $sequenceArray = [
            [
                'greyscale' => '',
                'width' => 50,
            ],
            [
                'height' => 100,
            ],
        ];

        $sequence = (new ManipulationSequence($sequenceArray));

        $this->assertEquals($sequenceArray, $sequence->toArray());
    }

    /** @test */
    public function it_does_not_return_empty_groups_when_iterating_a_merged_sequence()
    {
        $sequenceArray = [
            [
                'width' => 100,
                'height' => 100,
            ],
        ];

        $sequence1 = new ManipulationSequence();
        $sequence2 = new ManipulationSequence($sequenceArray);

        $mergedSequence = $sequence1->merge($sequence2);

        $this->assertCount(1, $mergedSequence);

        foreach ($mergedSequence as $sequence) {
            $this->assertEquals($sequenceArray[0], $sequence);
        }
    }

    /** @test */
    public function it_can_determine_that_the_sequence_is_empty()
    {
        $sequence = new ManipulationSequence();

        $this->assertTrue($sequence->isEmpty());

        $sequence->addManipulation('width', 50);

        $this->assertFalse($sequence->isEmpty());
    }
}

<?php

namespace Spatie\Image\Test;


use PHPUnit_Framework_TestCase;
use Spatie\Image\ManipulationSequence;

class ManipulationSequenceTest extends PHPUnit_Framework_TestCase
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
            ]
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
            ]
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
            ]
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
            ]
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
            ]
        ], $manipulationSequence->toArray());
    }

    /** @test */
    public function it_can_be_iterated_over()
    {
        $manipulationSequence = new ManipulationSequence();

        $manipulationSequence->addManipulation('height', 100);

        foreach ($manipulationSequence as $manipulationSet) {
            $this->assertEquals([
                'height' => 100
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
            ]
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
            ]
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
            ]
        ], $manipulationSequence1->toArray());
    }

}

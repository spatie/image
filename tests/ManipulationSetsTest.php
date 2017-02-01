<?php

namespace Spatie\Image\Test;


use PHPUnit_Framework_TestCase;
use Spatie\Image\ManipulationSets;

class ManipulationSetsTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_can_hold_an_empty_set()
    {
        $manipulationSets = new ManipulationSets();

        $this->assertEquals([], $manipulationSets->toArray());
    }

    /** @test */
    public function it_can_hold_a_manipulation()
    {
        $manipulationSets = new ManipulationSets();

        $manipulationSets->addManipulation('height', 100);

        $this->assertEquals([
            [
                'height' => 100,
            ]
        ], $manipulationSets->toArray());
    }

    /** @test */
    public function it_can_hold_multiple_manipulations()
    {
        $manipulationSets = new ManipulationSets();

        $manipulationSets
            ->addManipulation('height', 100)
            ->addManipulation('width', 200);

        $this->assertEquals([
            [
                'height' => 100,
                'width' => 200,
            ]
        ], $manipulationSets->toArray());
    }

    /** @test */
    public function it_will_replace_a_manipulation_if_its_applied_multiple_times()
    {
        $manipulationSets = new ManipulationSets();

        $manipulationSets
            ->addManipulation('height', 100)
            ->addManipulation('width', 200)
            ->addManipulation('height', 300);

        $this->assertEquals([
            [
                'height' => 300,
                'width' => 200,
            ]
        ], $manipulationSets->toArray());
    }

    /** @test */
    public function it_can_start_a_new_set()
    {
        $manipulationSets = new ManipulationSets();

        $manipulationSets
            ->addManipulation('height', 100)
            ->addManipulation('width', 200)
            ->startNewSet()
            ->addManipulation('height', 300);

        $this->assertEquals([
            [
                'height' => 100,
                'width' => 200,
            ],
            [
                'height' => 300,
            ]
        ], $manipulationSets->toArray());
    }

    /** @test */
    public function it_can_remove_a_manipulation()
    {
        $manipulationSets = new ManipulationSets();

        $manipulationSets
            ->addManipulation('height', 100)
            ->addManipulation('width', 200)
            ->removeManipulation('height');

        $this->assertEquals([
            [
                'width' => 200,
            ]
        ], $manipulationSets->toArray());
    }

    /** @test */
    public function it_can_be_iterated_over()
    {
        $manipulationSets = new ManipulationSets();

        $manipulationSets->addManipulation('height', 100);

        foreach ($manipulationSets as $manipulationSet) {
            $this->assertEquals([
                'height' => 100
            ], $manipulationSet);

        }
    }

}

<?php

namespace Spatie\Image\Test;

use Spatie\Image\Manipulations;
use Spatie\Image\Exceptions\InvalidManipulation;

class InvalidManipulationTest extends TestCase
{
    /** @var \Spatie\Image\Manipulations */
    protected $manipulations;

    public function setUp()
    {
        $this->manipulations = new Manipulations();
    }

    /** @test */
    public function an_exception_will_be_throw_when_passing_a_negative_number_to_width()
    {
        $this->expectException(InvalidManipulation::class);

        $this->manipulations->width(-50);
    }
}

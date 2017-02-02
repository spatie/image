<?php

namespace Spatie\Image\Test;

use PHPUnit_Framework_TestCase;
use Spatie\Image\Manipulations;

class ManipulationsTest extends PHPUnit_Framework_TestCase
{
    public function it_can()
    {
        $manipulations = new Manipulations();

        $manipulations->orientation(10);

        $manipulations->getManipulationSequence()->toArray();
    }
}
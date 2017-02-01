<?php

namespace Spatie\Image\Test;

use Spatie\Image\Image;
use PHPUnit_Framework_TestCase;
use Spatie\Image\Manipulations;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class ImageTest extends PHPUnit_Framework_TestCase
{
    /** @var TemporaryDirectory */
    protected $tempDir;

    public function setUp()
    {
        $this->tempDir = (new TemporaryDirectory())
            ->name('image-test')
            ->force()
            ->create()
            ->empty();
    }

    /** @test */
    public function it_can_modify_an_image_using_manipulations()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())
            ->manipulate(function (Manipulations $manipulations) {
                $manipulations
                    ->blur(20)
                    ->width(50);

            })
            ->save($targetFile);

        $this->assertFileExists($targetFile);

        echo $targetFile;
    }

    /** @test */
    public function it_can_modify_an_image_using_a_direct_manipulation_call()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())
            ->greyscale()
            ->save($targetFile);

        $this->assertFileExists($targetFile);

        exec("imagcat {$targetFile}");
    }

    protected function getTestJpg(): string
    {
        return __DIR__.'/testfiles/test.jpg';
    }
}

<?php

namespace Spatie\Image\Test;

use PHPUnit_Framework_TestCase;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class ImageTest extends PHPUnit_Framework_TestCase
{
    /** @var TemporaryDirectory */
    protected $tempDir;

    public function setUp()
    {
        (new TemporaryDirectory('imageTest', true))->delete();

        $this->tempDir = new TemporaryDirectory('imageTest', true);
    }

    /** @test */
    public function it_can_modify_an_image_using_manipulations()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::create($this->getTestJpg())
            ->manipulate(function(Manipulations $manipulations) {
                $manipulations->blur(20);
            })
            ->save(($targetFile));

        $this->assertFileExists($targetFile);

        echo $targetFile;
    }

    /** @test */
    public function it_can_modify_an_image_using_a_direct_manipulation_call()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::create($this->getTestJpg())
            ->blur(20)
            ->save(($targetFile));

        $this->assertFileExists($targetFile);

        echo $targetFile;
    }

    protected function getTestJpg(): string
    {
        return __DIR__.'/testfiles/test.jpg';
    }
}

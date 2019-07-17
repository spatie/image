<?php

namespace Spatie\Image\Test;

use Spatie\Image\Image;
use Spatie\Image\Manipulations;

class DuplicateImageTest extends TestCase
{
    /** @test */
    public function it_can_modify_multiple_images_with_same_filename()
    {
        $images = [
            $this->getTestFile('08/image.jpg'),
            $this->getTestFile('10/image.jpg'),
        ];

        $output_files = [];
        foreach ($images as $image) {
            $file_name = pathinfo($image, PATHINFO_FILENAME);
            $file_ext = pathinfo($image, PATHINFO_EXTENSION);
            $hash = md5($image);
            $output_file = $this->tempDir->path($file_name.'-'.$hash.'.'.$file_ext);
            $output_files[] = $output_file;
            Image::load($image)
                ->sepia()
                ->apply()
                ->crop(Manipulations::CROP_CENTER, 100, 100)
            ->save($output_file);
        }

        $this->assertFalse(file_get_contents($output_files[0]) === file_get_contents($output_files[1]));
    }
}

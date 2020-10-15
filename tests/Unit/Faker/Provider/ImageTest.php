<?php

namespace App\Tests\Unit\Faker\Provider;

use App\Faker\Provider\Image;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Testing fake image provider
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */
class ImageTest extends TestCase
{
    public function testImage()
    {
        $randomImage = Image::image();
        $this->assertIsString($randomImage);
        $this->assertFileExists($randomImage);

        $testImage = Image::image(null, null, null,null,true,null,null,null, 'test-image.jpg');
        $this->assertStringContainsString('test-image.jpg', $testImage);

        unlink($randomImage);
        unlink($testImage);
        $this->assertFileDoesNotExist($randomImage);
        $this->assertFileDoesNotExist($testImage);

        $this->expectException(InvalidArgumentException::class);
        Image::image('not-existent-dir');
    }
}
<?php

use Flashimage\Factory;
use Mockery as m;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Stream;

class FactoryTest extends PHPUnit_Framework_TestCase
{
    protected $types = ['gif', 'png', 'jpeg', 'bmp'];

    public function testLocalFile()
    {
        foreach ($this->types as $type) {
            $file = __DIR__.'/assets/10x5.'.$type;

            $factory = new Factory($file);

            $this->assertEquals($type, $factory->getType());
            $this->assertEquals([10, 5], $factory->getSize());
        }
    }

    public function testHttpResponse()
    {
        foreach ($this->types as $type) {
            $file = __DIR__.'/assets/10x5.'.$type;
            $stream = new Stream($file);

            $response = m::mock(ResponseInterface::class);
            $response->shouldReceive('getHeader')->with('Content-type')->once()->andReturn('image/'.$type);
            $response->shouldReceive('getBody')->withNoArgs()->once()->andReturn($stream);
            /** @var $response ResponseInterface */

            $factory = new Factory($response);

            $this->assertEquals($type, $factory->getType());
            $this->assertEquals([10, 5], $factory->getSize());
        }
    }
}

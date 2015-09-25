<?php
namespace Flashimage\Parser;

use Flashimage\StreamWalker;

abstract class SizeParser
{
    /**
     * The stream walker
     * @var StreamWalker
     */
    protected $stream;

    public function __construct(StreamWalker $stream)
    {
        $this->stream = $stream;
    }

    /**
     * Get the size of the image
     *
     * @return array
     */
    public function getSize()
    {
        $this->stream->resetPointer();

        return array_values($this->parseSize());
    }

    /**
     * Parse the image size from the blob
     *
     * @return mixed
     */
    abstract function parseSize();
}
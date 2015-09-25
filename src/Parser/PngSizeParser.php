<?php
namespace Flashimage\Parser;

class PngSizeParser extends SizeParser
{
    public function parseSize()
    {
        $chars = $this->stream->getChars(25);

        return unpack("N*", substr($chars, 16, 8));
    }
}
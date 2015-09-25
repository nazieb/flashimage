<?php
namespace Flashimage\Parser;

class GifSizeParser extends SizeParser
{
    function parseSize()
    {
        $chars = $this->stream->getChars(11);

        return unpack("S*", substr($chars, 6, 4));
    }
}
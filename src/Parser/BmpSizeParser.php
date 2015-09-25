<?php
namespace Flashimage\Parser;

class BmpSizeParser extends SizeParser
{
    function parseSize()
    {
        $chars = $this->stream->getChars(29);
        $chars = substr($chars, 14, 14);
        $type = unpack('C', $chars);

        return (reset($type) == 40) ? unpack('L*', substr($chars, 4)) : unpack('L*', substr($chars, 4, 8));
    }
}
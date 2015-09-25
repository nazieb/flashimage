<?php
namespace Flashimage\Parser;

class JpegSizeParser extends SizeParser
{
    function parseSize()
    {
        $state = null;

        while (true)
        {
            switch ($state)
            {
                default:
                    $this->stream->getChars(2);
                    $state = 'started';
                    break;

                case 'started':
                    $b = $this->stream->getByte();
                    if ($b === false) return false;
                    $state = $b == 0xFF ? 'sof' : 'started';
                    break;

                case 'sof':
                    $b = $this->stream->getByte();

                    if (in_array($b, range(0xe0, 0xef))) {
                        $state = 'skipframe';

                    } elseif (in_array($b, array_merge(range(0xC0,0xC3), range(0xC5,0xC7), range(0xC9,0xCB), range(0xCD,0xCF)))) {
                        $state = 'readsize';

                    } elseif ($b == 0xFF) {
                        $state = 'sof';

                    } else {
                        $state = 'skipframe';
                    }
                    break;

                case 'skipframe':
                    $skip = $this->readInt($this->stream->getChars(2)) - 2;
                    $state = 'doskip';
                    break;

                case 'doskip':
                    $this->stream->getChars($skip);
                    $state = 'started';
                    break;

                case 'readsize':
                    $c = $this->stream->getChars(7);
                    return array($this->readInt(substr($c, 5, 2)), $this->readInt(substr($c, 3, 2)));
            }
        }
    }

    protected function readInt($str)
    {
        $size = unpack("C*", $str);

        return ($size[1] << 8) + $size[2];
    }
}
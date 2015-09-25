<?php
namespace Flashimage;

use Psr\Http\Message\StreamInterface;

class StreamWalker
{
    /**
     * The stream object where the image blob is held
     * @var StreamInterface
     */
    protected $stream;

    /**
     * The current pointer position of the stream
     * @var int
     */
    protected $strpos = 0;

    /**
     * The internal cache of downloaded stream
     * @var string
     */
    protected $str;

    public function __construct(StreamInterface $stream)
    {
        $this->stream = $stream;
    }

    /**
     * Get the chars from the blob
     *
     * @param $n Number of characters needed
     * @return string|null
     */
    public function getChars($n)
    {
        $response = null;

        // do we need more data?
        if ($this->strpos + $n - 1 >= strlen($this->str)) {
            $end = ($this->strpos + $n);

            while (strlen($this->str) < $end && $response !== false) {
                // read more from the file handle
                $need = $end - $this->stream->tell();

                if ($response = $this->stream->read($need)) {
                    $this->str .= $response;

                } else {
                    return null;
                }
            }
        }

        $result = substr($this->str, $this->strpos, $n);
        $this->strpos += $n;

        // we are dealing with bytes here, so force the encoding
        return $result;
    }

    public function getByte()
    {
        $c = $this->getChars(1);
        $b = unpack("C", $c);

        return reset($b);
    }

    /**
     * Reset the pointer position
     */
    public function resetPointer()
    {
        $this->strpos = 0;
    }
}
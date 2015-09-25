<?php
namespace Flashimage;

use Flashimage\Parser\BmpSizeParser;
use Flashimage\Parser\GifSizeParser;
use Flashimage\Parser\JpegSizeParser;
use Flashimage\Parser\PngSizeParser;
use Flashimage\Parser\SizeParser;
use Ivory\HttpAdapter\HttpAdapterFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Zend\Diactoros\Stream;

/**
 * Flashimage - Get the type & size information of an image by fetching as little as possible
 *
 * This is a fork project of Fastimage by Tom Moor (https://github.com/tommoor/fastimage)
 * which is based on the Ruby Implementation by Steven Sykes (https://github.com/sdsykes/fastimage)
 */

class Factory
{
    /**
     * The stream walker
     * @var StreamWalker
     */
    protected $stream;

    /**
     * The Size parser
     * @var SizeParser
     */
    protected $size_parser = null;

    /**
     * The type of the image
     * @var string
     */
	protected $type;

	public function __construct($resource = null)
	{
		if (!empty($resource)) {
            $this->load($resource);
        }
	}

    /**
     * Load a resource, could be a Stream, a URL or a file
     *
     * @param $resource
     * @return bool
     */
	public function load($resource)
	{
		if (is_string($resource)) {
            if (filter_var($resource, FILTER_VALIDATE_URL) !== false) {
                $resource = $this->loadUrl($resource);

            } elseif (file_exists($resource)) {
                $file = fopen($resource, 'r');
                $resource = new Stream($file);
            }
        }

        if ($resource instanceof ResponseInterface) {
            $content_type = $resource->getHeader('Content-type');
            $this->type = str_replace('image/', '', explode(';', $content_type)[0]);

            $resource = $resource->getBody();
        }

        if ($resource instanceof StreamInterface) {
            $this->stream = new StreamWalker($resource);
            return true;
        }

        return false;
	}

    /**
     * Make a HTTP request to a URL and return its Response
     *
     * @param string $url
     * @return StreamInterface|null
     */
    public function loadUrl($url)
    {
        $http = HttpAdapterFactory::guess();

        $response = $http->get($url);

        if (
            $response->getStatusCode() >= 300 and
            $response->getStatusCode() < 400 and
            $response->hasHeader('Location')
        ) {
            $url = $response->getHeader('Location');
            $response = $http->get($url);
        }

        return $response;
    }

    /**
     * Get the size of the image
     *
     * @return array|null
     */
	public function getSize()
	{
        if (!$this->size_parser) {
            $type = $this->getType();

            $this->size_parser = $this->initSizeParser($type);
        }

        return $this->size_parser->getSize();
	}

    /**
     * Get the MIME type of the image
     *
     * @return string|null
     */
	public function getType()
	{
        if ($this->type) {
            return $this->type;
        }

        $this->stream->resetPointer();

        switch ($this->stream->getChars(2)) {
            case "BM":
                return $this->type = 'bmp';

            case "GI":
                return $this->type = 'gif';

            case chr(0xFF).chr(0xd8):
                return $this->type = 'jpeg';

            case chr(0x89).'P':
                return $this->type = 'png';

            default:
                return null;
        }
	}

    /**
     * Init a Size Parser
     *
     * @param $type
     * @return SizeParser|null
     */
    public function initSizeParser($type)
    {
        switch ($type) {
            case 'png':
                return new PngSizeParser($this->stream);

            case 'gif':
                return new GifSizeParser($this->stream);

            case 'bmp':
                return new BmpSizeParser($this->stream);

            case 'jpeg':
                return new JpegSizeParser($this->stream);

            default:
                return null;
        }
    }
}


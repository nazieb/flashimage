# FlashImage

Get the type &amp; size information of an image by fetching as little as possible.

This project is a fork of [Fastimage library by Tom Moor](https://github.com/tommoor/fastimage), which itself is a port of [Ruby implementation by Stephen Sykes](https://github.com/sdsykes/fastimage)

The main difference with the original library is that this one uses a PSR-7 compatible HTTP adapter to fetch image from the web, thanks to [Ivory HTTP library by Eric Geloen](https://github.com/egeloen/ivory-http-adapter) 

## Installation

The recommended way to install FlashImage is by using [Composer](https://getcomposer.org)

To add FlashImage as dependency to your project, add a dependency on nazieb/flashimage to your project's composer.json file. 

```php
{
	"require": {
		"nazieb/flashimage": "~1.0"
	}
}
````

## Usage

The main class of the FlashImage is the `Flashimage\Factory` which will load the image and initialize all the resource needed to fetch the data

```php	
$uri = "http://farm9.staticflickr.com/8151/7357346052_54b8944f23_b.jpg";
		
// loading image into constructor
$image = new Flashimage\Factory($uri);
list($width, $height) = $image->getSize();
echo "dimensions: " . $width . "x" . $height;

// or, create an instance and use the 'load' method
$image = new Flashimage\Factory();
$image->load($uri);
$type = $image->getType();
echo "filetype: " . $type;
```

### Supported Formats

Currently FlashImage only support 4 types of image: `png, jpeg, bmp, gif`.

More to come, or if you think you can contribute to support more formats, please send a Pull Request.

## References

* https://github.com/tommoor/fastimage
* https://github.com/sdsykes/fastimage
* http://pennysmalls.com/find-jpeg-dimensions-fast-in-pure-ruby-no-ima
* http://snippets.dzone.com/posts/show/805
* http://www.anttikupila.com/flash/getting-jpg-dimensions-with-as3-without-loading-the-entire-file/
* http://imagesize.rubyforge.org/


## License

MIT
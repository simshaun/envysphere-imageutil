<?php
require_once dirname(__FILE__) .'/../abstract/driver.php';

class EnvImage_Driver_Gd extends EnvImage_Abstract_Driver {

	protected $_formats = array(
		'jpg' => 'image/jpeg',
		'gif' => 'image/gif',
		'png' => 'image/png'
	);


	protected $orig_filename;

	public function setOrigFilename($orig_filename)
	{
		$this->orig_filename = $orig_filename;
	}

	public function getOrigFilename()
	{
		return $this->orig_filename;
	}


	protected $resource;

	public function setResource($resource)
	{
		$this->resource = $resource;
	}

	public function getResource()
	{
		return $this->resource;
	}


	public function getWidth()
	{
		return imagesx( $this->getResource() );
	}

	public function getHeight()
	{
		return imagesy( $this->getResource() );
	}


	protected $is_modified;

	public function isModified()
	{
		return $this->is_modified;
	}

	public function setModified()
	{
		$this->is_modified = TRUE;
	}


	protected $format;

	public function setFormat($format)
	{
		if ( ! in_array($format, array_keys($this->_formats)) )
			throw new Exception('Attempting to set an unsupported format: '. $format);

		$this->format = $format;
	}

	public function getFormat()
	{
		return $this->format;
	}

	public function getMimeType()
	{
		$format = $this->getFormat();
		return $this->_formats[$format];
	}


	protected $valid_file_extensions = array('jpg', 'jpeg', 'gif', 'png');

	public function isSupportedFile($filename)
	{
		$ext = $this->getFileExtension($filename);
		return in_array($ext, $this->valid_file_extensions);
	}

	// --------

	/**
	 * @param   string  $filename
	 * @return  EnvImage_Driver_Gd
	 */
	public function loadFromFile($filename)
	{
		if ( ! $this->isSupportedFile($filename))
			throw new Exception('Attempting to load unsupported file type: '. $this->getFileExtension($filename));

		if ( ! is_readable($filename))
			throw new Exception('Unable to read file: '. $filename);

		$ext = $this->getFileExtension($filename);

		$func = NULL;
		switch ($ext)
		{
			case 'jpg':
			case 'jpeg':
				$func = 'imagecreatefromjpeg';
				$this->setFormat('jpg');
				break;
			case 'gif':
				$func = 'imagecreatefromgif';
				$this->setFormat('gif');
				break;
			case 'png';
				$func = 'imagecreatefrompng';
				$this->setFormat('png');
				break;
		}

		$this->setOrigFilename( basename($filename) );
		$this->setResource( call_user_func($func, $filename) );

		return $this;
	}

	/**
	 * Outputs the image as a string.
	 *
	 * @param   int  $quality  JPG: 0-100, PNG: 0-9
	 * @return  string
	 */
	public function output($quality = 100)
	{
		ob_start();

		switch ( $this->getFormat() )
		{
			case 'jpg':
			case 'jpeg':
				imagejpeg($this->getResource(), NULL, $quality);
				break;
			case 'gif':
				imagegif($this->getResource(), NULL);
				break;
			case 'png':
				// imagepng()'s quality ranges from 0-9
				if ($quality > 9)
					$quality = ceil($quality / 10) - 1;

				imagepng($this->getResource(), NULL, $quality);
				break;
		}

		return ob_get_clean();
	}

	/**
	 * Saves the image to a file. Does not output.
	 *
	 * If $filename is empty, the original filename will be used.
	 * If the original filename is empty (e.g. if the source came from a string),
	 * a filename will be automatically generated.
	 *
	 * @param   string  $directory  Path to directory where image will be saved.
	 * @param   string  $filename   Filename that image will be saved as.
	 * @param   bool|string  $sanitize  If not FALSE, will be used as a callback for sanitizing filename.
	 * @param   int     $quality    JPG: 0-100, PNG: 0-9
	 * @return  string  The full path the image was saved to.
	 */
	public function save($directory, $filename = NULL, $sanitize = TRUE, $quality = 100)
	{
		// Make sure the directory ends with the (proper) directory separator.
		$directory = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

		if ( ! is_writable($directory))
			throw new Exception('Could not save the image because the directory "'. $directory .'" is not writable.');

		if (empty($filename))
		{
			$filename = $this->getOrigFilename();

			if (empty($filename)) // generate a random filename
				$filename = md5( uniqid(rand(2500, 10000000), TRUE) );
			else // strip the extension off the original filename.
				$filename = $this->getFileName( $filename );
		}
		else
			$filename = $this->getFileName($filename);

		$filename .= '.'. $this->getFormat();

		if ($sanitize !== FALSE)
		{
			if ($sanitize === TRUE)
			{
				$filename = $this->sanitizeFilename( $filename );
			}
			elseif ( function_exists($sanitize) )
			{
				$filename = call_user_func( $sanitize, $filename );
			}
		}

		$image = $this->output($quality);

		$handle = @fopen($directory . $filename, 'w');

		if ($handle === FALSE)
			throw new Exception('Failed to create new file that image would be written to.');

		flock($handle, LOCK_EX);
		fwrite($handle, $image);
		flock($handle, LOCK_UN);
		fclose($handle);

		return $directory . $filename;
	}

	/**
	 * Resizes the image, if necessary. If the image's dimensions are smaller than the
	 * dimensions passed as arguments to this method, no resize is performed.
	 *
	 * @param   int       $max_width
	 * @param   null|int  $max_height       If null, $height set to $width.
	 * @param   bool      $crop             Crop to ensure output is exactly $width and $height.
	 * @param   string    $crop_direction  (t, b, l, r, c) If $crop, which direction to crop the image towards.
	 * @return  EnvImage_Driver_Gd
	 */
	public function resize($max_width = 0, $max_height = 0, $crop = FALSE, $crop_direction = 'c')
	{
		if (( ! empty($max_width) && ! is_numeric($max_width)) || ( ! empty($max_height) && ! is_numeric($max_height)) )
			throw new Exception('Attempting to resize image to an invalid unit.');

		if ($max_width == 0 && $max_height == 0)
			throw new Exception('Image can not be resized to 0x0.');

		// Do we need to resize the image?
		if ($max_width > $this->getWidth() && $max_height > $this->getHeight())
			return $this;

		$old_width  = $this->getWidth();
		$old_height = $this->getHeight();

		if ( ! empty($crop_direction))
		{
			$crop_direction = strtolower($crop_direction);
			$crop_direction = $crop_direction[0];
		}

		// Find the aspect ratio.
		$ar = $old_width / $old_height;

		// If $crop, crop...
		if ($crop && $old_width > $old_height)
		{
			switch ($crop_direction)
			{
				case 'l':
					$x = 0;
					break;
				case 'r':
					$x = floor( $old_width - $old_height );
					break;
				default:
					$x = floor( ($old_width - $old_height) / 2 );
			}

			$this->crop($x, 0, $old_height, $old_height);
			$old_width = $old_height;
			$ar = 1;
		}
		elseif ($crop && $old_width < $old_height)
		{
			switch ($crop_direction)
			{
				case 't':
					$y = 0;
					break;
				case 'b':
					$y = floor( $old_height - $old_width );
					break;
				default:
					$y = floor( ($old_height - $old_width) / 2 );
			}

			$this->crop(0, $y, $old_width, $old_width);
			$old_height = $old_width;
			$ar = 1;
		}

		// Calculate the new dimensions.
		if ($max_width == 0)
		{
			$new_height = $old_height > $max_height ? $max_height : $old_height;
			$new_width = floor( $new_height / $ar );
		}
		elseif ($max_height == 0)
		{
			$new_width = $old_width > $max_width ? $max_width : $old_width;
			$new_height = floor( $new_width / $ar );
		}
		else
		{
			// Use the smallest max dimensions and calculate the new width of the other dimension.
			if ($max_width > $max_height)
			{
				$new_height = $old_height > $max_height ? $max_height : $old_height;
				$new_width = floor( $new_height * $ar );

				if ($new_width > $max_width)
				{
					$new_width = $max_width;
					$new_height = floor( $new_width / $ar );
				}
			}
			elseif ($max_width < $max_height)
			{
				$new_width = $old_width > $max_width ? $max_width : $old_width;
				$new_height = floor( $new_width / $ar );

				if ($new_height > $max_height)
				{
					$new_height = $max_height;
					$new_width = floor( $new_width * $ar );
				}
			}
			// If the two max dimensions are equal, determine which dimension to resize on
			// based on the source image.
			else {
				// If the image is taller than it is wide, resize based on the height.
				if ($old_height > $old_width) {
					$new_height = $old_height > $max_height ? $max_height : $old_height;
					$new_width = floor( $max_height * $ar );

					while ($new_width > $max_width || $new_height > $max_height) {
						$new_width = $max_width;
						$new_height = floor( $new_width / $ar );
					}
				}
				// If the image is wider than it is tall, or is equal in both width and height, resize based on width.
				else {
					$new_width = $old_width > $max_width ? $max_width : $old_width;
					$new_height = floor( $max_width / $ar );

					while ($new_width > $max_width || $new_height > $max_height) {
						$new_height = $max_height;
						$new_width = floor( $new_height / $ar );
					}
				}
			}
		}

		$new_resource = imagecreatetruecolor($new_width, $new_height);
		imagecopyresampled($new_resource, $this->getResource(), 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);
		$this->setResource($new_resource);
		unset($new_resource);
		$this->setModified();

		return $this;
	}

	/**
	 * Crops the image, from ($x, $y) to ($x + $width, $y + $height).
	 *
	 *    $image->crop(0, 0, 200, 200);
	 *
	 * @param   $x
	 * @param   $y
	 * @param   $width
	 * @param   $height
	 * @return  EnvImage_Driver_Gd
	 */
	public function crop($x, $y, $width, $height)
	{
		$new_resource = imagecreatetruecolor($width, $height);
		imagecopy($new_resource, $this->getResource(), 0, 0, $x, $y, $width, $height);
		$this->setResource($new_resource);
		unset($new_resource);
		$this->setModified();

		return $this;
	}

	/**
	 * Rotates an image by the number of degrees specified.
	 *
	 *    $image->rotate(25, 0xFFFFFF);
	 *
	 * @param  int  -360 to 360
	 * @param  int  HEX background color of the area generated by the rotate function
	 * @return  EnvImage_Driver_Gd
	 */
	public function rotate($angle, $bg_color = 0x000000) {
		$this->setResource( imagerotate($this->getResource(), $angle, $bg_color) );

		// If the image format is gif or png, make the new background transparent.
		if ($this->getFormat() == 'gif' || $this->getFormat() == 'png')
		{
			$rgb = $this->hex2rgb($bg_color);
			$color = imagecolorallocate($this->getResource(), $rgb['r'], $rgb['g'], $rgb['b']);
			imagecolortransparent($this->getResource(), $color);
		}

		$this->setModified();

		return $this;
	}
}
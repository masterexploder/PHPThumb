<?php

/**
 * phpThumb plugin which adds support for a new background fill option whereby the image is
 * padded to a particular size.
 */
class BackgroundFillLib {

	/**
	 * Instance of GdThumb passed to this class
	 *
	 * @var $parentInstance
	 */
	protected $parentInstance;
	protected $currentDimensions;
	protected $workingImage;
	protected $newImage;

	/**
	 * Background fill an image using the provided color
	 *
	 * @param int $width The desired width of the new image
	 * @param int $height The desired height of the new image
	 * @param string the desired pad color, must be rgb format
	 */
	public function backgroundFillWithColor( $width, $height, $color, &$that ) {

		if ( ! is_array( $color ) && strlen( $color ) == 3 )
			$color = (float) str_pad( (string) $color, 9, $color ) . '000';

		if ( ! is_array( $color ) )
			$color = array( 'top' => $color, 'bottom' => $color, 'left' => $color, 'right' => $color );

		$this->backgroundFillColor( $width, $height, $color, $that );

	}

	/**
	 * Background fill an image matching the pad color to the color of
	 * each edge of the image
	 *
	 * @param int $width The desired width of the new image
	 * @param int $height The desired height of the new image
	 */
	public function backgroundFillAutoColor( $width, $height, &$that ) {

		$this->parentInstance 		= $that;
		$this->currentDimensions 	= $this->parentInstance->getCurrentDimensions();
		$this->workingImage			= $this->parentInstance->getWorkingImage() ? $this->parentInstance->getWorkingImage() : $this->parentInstance->getOldImage();
		$this->newImage				= $this->parentInstance->getOldImage();		// Colors

		$left		= array_count_values( $this->getColorsBetweenCorners( 'topleft', 'bottomleft', $this->workingImage ) );
		$right 		= array_count_values( $this->getColorsBetweenCorners( 'topright', 'bottomright', $this->workingImage ) );
		$top 		= array_count_values( $this->getColorsBetweenCorners( 'topleft', 'topright', $this->workingImage ) );
		$bottom 	= array_count_values( $this->getColorsBetweenCorners( 'bottomleft', 'bottomright', $this->workingImage ) );

		arsort( $left );
		arsort( $right );
		arsort( $top );
		arsort( $bottom );

		$colors['left']		= key( $left );
		$colors['right']	= key( $right );
		$colors['top']		= key( $top );
		$colors['bottom'] 	= key( $bottom );

		$this->backgroundFillColor( $width, $height, $colors, $that );

	}

	/**
	 * Background fill an image using the provided color
	 *
	 * @param int $width The desired width of the new image
	 * @param int $height The desired height of the new image
	 * @param Array the desired pad colors in RGB format, array should be array( 'top' => '', 'right' => '', 'bottom' => '', 'left' => '' );
	 */
	private function backgroundFillColor( $width, $height, Array $colors, &$that ) {

		$this->parentInstance 		= $that;
		$this->currentDimensions 	= $this->parentInstance->getCurrentDimensions();
		$this->workingImage			= $this->parentInstance->getWorkingImage() ? $this->parentInstance->getWorkingImage() : $this->parentInstance->getOldImage();
		$this->newImage				= $this->parentInstance->getOldImage();

		$offsetLeft = ( $width - $this->currentDimensions['width'] ) / 2;
		$offsetTop = ( $height - $this->currentDimensions['height'] ) / 2;

		$this->newImage = imagecreatetruecolor( $width, $height );

		// This is needed to support alpha
		imagesavealpha( $this->newImage, true );
		imagealphablending( $this->newImage, false );

		// Check if we are padding vertically or horizontally
		if ( $this->currentDimensions['width'] != $width ) {

			$colorToPaint = imagecolorallocatealpha( $this->newImage, substr( $colors['left'], 0, 3 ), substr( $colors['left'], 3, 3 ), substr( $colors['left'], 6, 3 ), substr( $colors['left'], 9, 3 ) );

			// Fill left color
	        imagefilledrectangle( $this->newImage, 0, 0, $offsetLeft + 5, $height, $colorToPaint );

			$colorToPaint = imagecolorallocatealpha( $this->newImage, substr( $colors['right'], 0, 3 ), substr( $colors['right'], 3, 3 ), substr( $colors['right'], 6, 3 ), substr( $colors['left'], 9, 3 ) );

			// Fill right color
	        imagefilledrectangle( $this->newImage, $offsetLeft + $this->currentDimensions['width'] - 5, 0, $width, $height, $colorToPaint );

		} elseif ( $this->currentDimensions['height'] != $height ) {

			$colorToPaint = imagecolorallocatealpha( $this->newImage, substr( $colors['top'], 0, 3 ), substr( $colors['top'], 3, 3 ), substr( $colors['top'], 6, 3 ), substr( $colors['left'], 9, 3 ) );

			// Fill top color
	        imagefilledrectangle( $this->newImage, 0, 0, $width, $offsetTop + 5, $colorToPaint );

			$colorToPaint = imagecolorallocatealpha( $this->newImage, substr( $colors['bottom'], 0, 3 ), substr( $colors['bottom'], 3, 3 ), substr( $colors['bottom'], 6, 3 ), substr( $colors['left'], 9, 3 ) );

			// Fill bottom color
	        imagefilledrectangle( $this->newImage, 0, $offsetTop - 5 + $this->currentDimensions['height'], $width, $height, $colorToPaint );

		}

		imagecopy( $this->newImage, $this->workingImage, $offsetLeft, $offsetTop, 0, 0, $this->currentDimensions['width'], $this->currentDimensions['height'] );

		$this->parentInstance->setOldImage( $this->newImage );

		return $that;

	}

	public function canBackgroundFillSolidColorWithResize( $width, $height, &$that ) {

		$this->parentInstance 		= $that;
		$this->currentDimensions 	= $this->parentInstance->getCurrentDimensions();
		$this->workingImage			= $this->parentInstance->getWorkingImage();

		// Resize the image (not adaptive) to see where we need to pad
		$thumb = phpThumbFactory::create( $that->getFileName(), array( 'jpegQuality' => 100 ) );
		$thumb->resize( $width, $height );

		$this->testImage = $thumb->getWorkingImage();
		$this->currentDimensions = $thumb->getCurrentDimensions();

		// Check if we are padding vertical / horizontally
		if ( $this->currentDimensions['width'] != $width ) {

			if ( count( array_unique( $this->getColorsBetweenCorners( 'topleft', 'bottomleft', $this->testImage ) ) ) > 10 )
				return false;

			if ( count( array_unique( $this->getColorsBetweenCorners( 'topright', 'bottomright', $this->testImage ) ) ) > 10 )
				return false;

			return true;

		} elseif ( $this->currentDimensions['height'] != $height ) {

			if ( count( array_unique( $this->getColorsBetweenCorners( 'topleft', 'topright', $this->testImage ) ) ) > 10 )
				return false;

			if ( count( array_unique( $this->getColorsBetweenCorners( 'bottomleft', 'bottomright', $this->testImage ) ) ) > 10 )
				return false;

			return true;

		}

	}

	function getColorsBetweenCorners( $corner1, $corner2, $resource ) {

		$colors = array();
		$dimensions = $this->currentDimensions;

		$x_1 = strpos( $corner1, 'left' ) === false ? $dimensions['width'] - 1 : 0;
		$x_2 = strpos( $corner2, 'left' ) === false ? $dimensions['width'] - 1 : 0;

		$y_1 = strpos( $corner1, 'top' ) === false ? $dimensions['height'] - 1 : 0;
		$y_2 = strpos( $corner2, 'top' ) === false ? $dimensions['height'] - 1 : 0;


		// Vertical
		if ( $x_1 == $x_2 ) {

			while( $y_1 < $y_2 ) {
				$colors[] = $this->colorAtToRGBA( imagecolorat( $resource, $x_1, $y_1 ), $resource );
				$y_1++;
			}

		}

		// Horizontal
		elseif ( $y_1 == $y_2 ) {
			while ( $x_1 < $x_2 ) {
				$colors[] = $this->colorAtToRGBA( imagecolorat( $resource, $x_1, $y_1 ), $resource );
				$x_1++;
			}
		}

		return $colors;

	}

    function colorAtToRGBA( $rgba, $resource ) {

		$colors = imagecolorsforindex( $resource, $rgba );

		$colors['red'] 		= str_pad( (string) $colors['red'], 3, '0', STR_PAD_LEFT );
		$colors['green'] 	= str_pad( (string) $colors['green'], 3, '0', STR_PAD_LEFT );
		$colors['blue']		= str_pad( (string) $colors['blue'], 3, '0', STR_PAD_LEFT );
		$colors['alpha'] 	= str_pad( (string) $colors['alpha'], 3, '0', STR_PAD_LEFT );

		return "{$colors['red']}{$colors['green']}{$colors['blue']}{$colors['alpha']}";

    }
}

$pt = PhpThumb::getInstance();
$pt->registerPlugin( 'BackgroundFillLib', 'gd' );
<?php

class BackgroundFillLib
{
	/**
	 * Instance of GdThumb passed to this class
	 * 
	 * @var GdThumb
	 */
	protected $parentInstance;
	protected $currentDimensions;
	protected $workingImage;
	protected $newImage;
	
	public function backgroundFillColorAuto( $width, $height, &$that ) {
		
		$this->currentDimensions 	= $this->parentInstance->getCurrentDimensions();
		$this->workingImage			= $this->parentInstance->getWorkingImage() ? $this->parentInstance->getWorkingImage() : $this->parentInstance->getOldImage();
		$this->newImage				= $this->parentInstance->getOldImage();
		
		
		$offsetLeft = ( $width - $this->currentDimensions['width'] ) / 2;
		$offsetTop = ( $height - $this->currentDimensions['height'] ) / 2;
		
		$this->newImage = imagecreatetruecolor($width, $height);
		
		//check if we are padding vertical / horizontally
		if( $this->currentDimensions['width'] != $width ) {
		
			//Pad left / right
			$left_color = $this->getColorsBetweenCorners( 'topleft', 'bottomleft', $this->workingImage );
			$right_color = $this->getColorsBetweenCorners( 'topright', 'bottomright', $this->workingImage );
			
			$left_color = array_count_values( $left_color );
			$right_color = array_count_values( $right_color );
			
			arsort($left_color);
			arsort($right_color);

			$left_color = key( $left_color );
			$right_color = key( $right_color );	

			$colorToPaint = imagecolorallocatealpha($this->newImage,substr($left_color, 0, 3),substr($left_color, 3, 3),substr($left_color, 6, 3),0);
			
			// Fill left color
	        imagefilledrectangle($this->newImage,0,0,$offsetLeft+5,$height,$colorToPaint);
	        
			
			$colorToPaint = imagecolorallocatealpha($this->newImage,substr($right_color, 0, 3),substr($right_color, 3, 3),substr($right_color, 6, 3),0);
			// Fill right color
	        imagefilledrectangle($this->newImage,$offsetLeft+$this->currentDimensions['width']-5,0,$width,$height,$colorToPaint);
			
		} elseif( $this->currentDimensions['height'] != $height ) {
		
			//Pad top / bottom
			$top_color = $this->getColorsBetweenCorners( 'topleft', 'topright', $this->workingImage );
			$bottom_color = $this->getColorsBetweenCorners( 'bottomleft', 'bottomright', $this->workingImage );
			
			$top_color = array_count_values( $top_color );
			$bottom_color = array_count_values( $bottom_color );
			
			arsort($top_color);
			arsort($bottom_color);

			$top_color = key( $top_color );
			$bottom_color = key( $bottom_color );	

			$colorToPaint = imagecolorallocatealpha($this->newImage,substr($top_color, 0, 3),substr($top_color, 3, 3),substr($top_color, 6, 3),0);
			
			// Fill left color
	        imagefilledrectangle($this->newImage,0,0,$width,$offsetTop+5,$colorToPaint);
	        
			
			$colorToPaint = imagecolorallocatealpha($this->newImage,substr($bottom_color, 0, 3),substr($bottom_color, 3, 3),substr($bottom_color, 6, 3),0);
			// Fill right color
	        imagefilledrectangle($this->newImage,0,$offsetTop-5+$this->currentDimensions['height'],$width,$height,$colorToPaint);

		}

		imagecopy($this->newImage, $this->workingImage, $offsetLeft, $offsetTop, 0, 0, $this->currentDimensions['width'], $this->currentDimensions['height']);
		
		$this->parentInstance->setOldImage($this->newImage);
		
		return $that;
		
	}
	
	public function canBackgroundFillSolidColorWithResize( $width, $height, &$that ) {

		$this->parentInstance 		= $that;
		$this->currentDimensions 	= $this->parentInstance->getCurrentDimensions();
		$this->workingImage			= $this->parentInstance->getWorkingImage();
		
		// Resize the image (not adaptive) to see where we need to 		
		$thumb = phpThumbFactory::create( $that->getFileName(), array( 'jpegQuality' => 100 ) );
		$thumb->resize( $width, $height );
		
		$this->testImage = $thumb->getWorkingImage();		
		$this->currentDimensions = $thumb->getCurrentDimensions();
		
		
		//check if we are padding vertical / horizontally
		if( $this->currentDimensions['width'] != $width ) {
		
			//Pad left / right
			$left_colors = array_unique( $this->getColorsBetweenCorners( 'topleft', 'bottomleft', $this->testImage ) );
						
			if( count( $left_colors ) > 10 )
				return false;
			
			$right_colors = array_unique( $this->getColorsBetweenCorners( 'topright', 'bottomright', $this->testImage ) );

			if( count( $right_colors ) > 10 )
				return false;
			
			return true;
			
		} elseif( $this->currentDimensions['height'] != $height ) {
		
			//Pad top / bottom
			
			$top_colors = array_unique( $this->getColorsBetweenCorners( 'topleft', 'topright', $this->testImage ) );
			
			if( count( $top_colors ) > 10 )
				return false;
			
			$bottom_colors = array_unique( $this->getColorsBetweenCorners( 'bottomleft', 'bottomright', $this->testImage ) );
			
			if( count( $bottom_colors ) > 10 )
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
		
		
		//vertical
		if( $x_1 == $x_2 ) {
			
			while( $y_1 < $y_2 ) {
				$colors[] = $this->roundRGB( $this->colorAtToRGB( imagecolorat( $resource, $x_1, $y_1 ) ) );
				$y_1++;
			}
			
		} 
		
		//horizontal
		elseif( $y_1 == $y_2 ) {
			while( $x_1 < $x_2 ) {
				$colors[] = $this->roundRGB( $this->colorAtToRGB( imagecolorat( $resource, $x_1, $y_1 ) ) );
				$x_1++;
			}
		}
		
		return $colors;
	
	}
    
    function colorAtToRGB( $rgb ) {
    	$r = ($rgb >> 16) & 0xFF;
		$g = ($rgb >> 8) & 0xFF;
		$b = $rgb & 0xFF;
		
		return ( (string) $r ) . ( (string) $g ) . ( (string) $b );
    }
    
    function roundRGB( $rgb, $tolerance = 4 ) {
    	
    	$r = substr( $rgb, 0, 3 );
	   	$g = substr( $rgb, 3, 3 );    	
    	$b = substr( $rgb, 6, 3 );
    	
    	//$r = ceil($r / $tolerance) * $tolerance;
      	//$g = ceil($g / $tolerance) * $tolerance;
    	//$b = ceil($b / $tolerance) * $tolerance;

    	return "{$r}{$g}{$b}";
    	
    }
}

$pt = PhpThumb::getInstance();
$pt->registerPlugin('BackgroundFillLib', 'gd');
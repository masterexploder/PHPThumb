<?php
/**
 * GD Tile Lib Plugin Definition File
 *
 * This file contains the plugin definition for the GD Tile Lib for PHP Thumb
 *
 * PHP Version 5 with GD 2.0+
 * PhpThumb : PHP Thumb Library <http://phpthumb.gxdlabs.com>
 * Copyright (c) 2009, Ian Selby/Gen X Design
 *
 * Author(s): Mario Rutz <mario@basis42.de>
 *
 * Licensed under the MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author Mario Rutz <mario@basis42.de>
 * @copyright Copyright (c) 2010 basis42
 * @link http://www.basis42.de
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version 3.0
 * @package PhpThumb
 * @filesource
 */

/**
 * GD Reflection Lib Plugin
 *
 * This plugin allows you to create a tiled version of your image
 *
 * @package PhpThumb
 * @subpackage Plugins
 */
class GdTileLib
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
	protected $options;
	protected $xTiles = 1;
	protected $yTiles = 1;
	protected $tiles = array();
	protected $tilesDir;
	
	public function createTiles ($xTiles, $yTiles, $tilesDir, &$that)
	{
		// bring stuff from the parent class into this class...
		$this->parentInstance 		= $that;
		$this->currentDimensions 	= $this->parentInstance->getCurrentDimensions();
		$this->workingImage			= $this->parentInstance->getWorkingImage();
		$this->newImage				= $this->parentInstance->getOldImage();
		$this->options				= $this->parentInstance->getOptions();
		$this->tilesDir				= $tilesDir;
		
		$width				= $this->currentDimensions['width'];
		$height				= $this->currentDimensions['height'];

		$this->xTiles = $xTiles;
		$this->yTiles = $yTiles;

		$tileWidth = intval($width / $xTiles);
		$tileHeight = intval($height / $yTiles);

		for($y = 0; $y < $this->yTiles; $y++){
			for($x = 0; $x < $this->xTiles; $x++){
				$xStart = $x * $tileWidth;
				$yStart = $y * $tileHeight;

				$actTileWidth = $tileWidth;
				if($xStart + $tileWidth > $width){
					$actTileWidth = $width - ($xStart + $tileWidth);
				}
				$actTileHeight = $tileHeight;
				if($yStart + $tileHeight > $height){
					$actTileHeight = $height - ($yStart + $tileHeight);
				}
				$image = imagecreatetruecolor($actTileWidth, $actTileHeight);
				ImageCopy ( $image, $this->newImage , 0 , 0 , $xStart , $yStart , $tileWidth , $tileHeight );

				$tileKey = $x .'_'. $y;
				$this->tiles[$tileKey] = $image;
			}
		}
			
		$this->saveTiles();
		return $that;
	}

	public function showTiles(){
		$format = $this->parentInstance->getFormat();
		$fileName = basename($this->parentInstance->getFileName());
		$parentDir = dirname($this->parentInstance->getFileName());
		
		for($y = 0; $y < $this->yTiles; $y++){
			for($x = 0; $x < $this->xTiles; $x++){
				$tileKey = $x .'_'. $y;
				switch ($format)
				{
					case 'GIF':
						echo '<img src="'.$this->tilesDir.'/' . preg_replace('/\.gif/i', '', $fileName) . '_' .$tileKey . '.gif">' ;
						break;
					case 'JPG':
						echo '<img src="'.$this->tilesDir.'/' . preg_replace('/\.jpg/i', '', $fileName) . '_' .$tileKey . '.jpg">' ;
						break;
					case 'PNG':
						echo '<img src="'.$this->tilesDir.'/' . preg_replace('/\.png/i', '', $fileName) . '_' .$tileKey . '.png">' ;
						break;
				}
			}
			echo "<br>";
		}
	}

	private function saveTiles(){
		
		$format = $this->parentInstance->getFormat();
		$fileName = basename($this->parentInstance->getFileName());
		$parentDir = dirname($this->parentInstance->getFileName());
		
		if($this->parentInstance->isRemoteImage() && $this->tilesDir == null){
			throw new RuntimeException ('tilesDir can\'t be empty when dealing with remote images.');
		}
		else{
			$parentDir = dirname($this->tilesDir);
		}
		
		// make sure the directory is writeable
		if (!is_writeable(dirname($parentDir)))
		{
			// try to correct the permissions
			if ($this->options['correctPermissions'] === true)
			{
				@chmod(dirname($parentDir), 0777);
				
				// throw an exception if not writeable
				if (!is_writeable(dirname($parentDir)))
				{
					throw new RuntimeException ('File is not writeable, and could not correct permissions: ' . $parentDir);
				}
			}
			// throw an exception if not writeable
			else
			{
				throw new RuntimeException ('File not writeable: ' . $parentDir);
			}
		}
		
		// if tiles dir not yet exists create it
		if(!file_exists($this->tilesDir)){
			mkdir($this->tilesDir);	
		}
		
		foreach ($this->tiles as $tilename => $tile){
			switch ($format)
			{
				case 'GIF':
					imagegif($tile, $this->tilesDir . "/" . preg_replace('/\.gif/i', '', $fileName) . '_' .$tilename . '.gif');
					break;
				case 'JPG':
					$opts = $this->parentInstance->getOptions();
					imagejpeg($tile, $this->tilesDir . "/" . preg_replace('/\.jpg/i', '', $fileName) . '_' .$tilename . '.jpg', $opts['jpegQuality']);
					break;
				case 'PNG':
					imagepng($tile, $this->tilesDir . "/" . preg_replace('/\.png/i', '', $fileName) . '_' .$tilename . '.png');
					break;
			}
		}
	}
}

$pt = PhpThumb::getInstance();
$pt->registerPlugin('GdTileLib', 'gd');
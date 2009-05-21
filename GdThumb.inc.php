<?php
/**
 * PhpThumb GD Thumb Class Definition File
 * 
 * This file contains the definition for the GdThumb object
 * 
 * @author Ian Selby <ian@gen-x-design.com>
 * @copyright Copyright 2009 Gen X Design
 * @version 3.0
 * @package PhpThumb
 * @filesource
 */

/**
 * GdThumb Class Definition
 * 
 * This is the GD Implementation of the PHP Thumb library.
 * 
 * @package PhpThumb
 * @subpackage Core
 */
class GdThumb extends ThumbBase
{
	/**
	 * Class Constructor
	 * 
	 * @return GdThumb 
	 * @param string $fileName
	 */
	public function __construct ($fileName)
	{
		parent::__construct($fileName);
		
		$this->determineFormat();
		$this->verifyFormatCompatiblity();
	}
	
	/**
	 * Determines the file format by mime-type
	 * 
	 * This function will throw exceptions for invalid images / mime-types
	 * 
	 */
	protected function determineFormat ()
	{
		$formatInfo = getimagesize($this->fileName);
		
		// non-image files will return false
		if ($formatInfo === false)
		{
			$this->triggerError('File is not a valid image: ' . $this->fileName);
		}
		
		$mimeType = isset($formatInfo['mime']) ? $formatInfo['mime'] : null;
		
		switch ($mimeType)
		{
			case 'image/gif':
				$this->format = 'GIF';
				break;
			case 'image/jpeg':
				$this->format = 'JPG';
				break;
			case 'image/png':
				$this->format = 'PNG';
				break;
			default:
				$this->triggerError('Image format not supported: ' . $mimeType);
		}
	}
	
	/**
	 * Makes sure the correct GD implementation exists for the file type
	 * 
	 */
	protected function verifyFormatCompatiblity ()
	{
		$isCompatible 	= true;
		$gdInfo			= gd_info();
		
		switch ($this->format)
		{
			case 'GIF':
				$isCompatible = $gdInfo['GIF Create Support'];
				break;
			case 'JPG':
			case 'PNG':
				$isCompatible = $gdInfo[$this->format . ' Support'];
				break;
			default:
				$isCompatible = false;
		}
		
		if (!$isCompatible)
		{
			$this->triggerError('Your GD installation does not support ' . $this->format . ' image types');	
		}
	}
}

<?php

namespace PHPThumb;

/**
 * PhpThumb Base Class Definition File
 * 
 * This file contains the definition for the ThumbBase object
 * 
 * PHP Version 5 with GD 2.0+
 * PhpThumb : PHP Thumb Library <http://phpthumb.gxdlabs.com>
 * Copyright (c) 2009, Ian Selby/Gen X Design
 * 
 * Author(s): Ian Selby <ian@gen-x-design.com>
 * 
 * Licensed under the MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @author Ian Selby <ian@gen-x-design.com>
 * @copyright Copyright (c) 2009 Gen X Design
 * @link http://phpthumb.gxdlabs.com
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version 3.0
 * @package PhpThumb
 * @filesource
 */

/**
 * ThumbBase Class Definition
 * 
 * This is the base class that all implementations must extend.  It contains the 
 * core variables and functionality common to all implementations, as well as the functions that 
 * allow plugins to augment those classes.
 * 
 * @package PhpThumb
 * @subpackage Core
 */
abstract class PHPThumb
{	
	/**
	 * The name of the file we're manipulating
	 * 
	 * This must include the path to the file (absolute paths recommended)
	 * 
	 * @var string
	 */
	protected $fileName;
	
	/**
	 * What the file format is (mime-type)
	 * 
	 * @var string
	 */
	protected $format;
	
	/**
	 * Whether or not the image is hosted remotely
	 * 
	 * @var bool
	 */
	protected $remoteImage;
	
	/**
	 * An array of attached plugins to execute in order.
	 * @var array
	 */
	protected $plugins;
	
	
	public function __construct($fileName, array $options = array(), array $plugins = array())
	{
		$this->fileName				= $fileName;
		$this->remoteImage			= false;
		
		$this->fileExistsAndReadable();
		$this->setOptions($options);
		$this->plugins = $plugins;
	}
	
	abstract public function setOptions(array $options = array());
	
	/**
	 * Checks to see if $this->fileName exists and is readable
	 * 
	 */
	protected function fileExistsAndReadable()
	{
		if (preg_match('/https?:\/\//', $this->fileName) !== 0)
		{
			$this->remoteImage = true;
			return;
		}
		
		if (!file_exists($this->fileName))
		{
			throw new \InvalidArgumentException('Image file not found: ' . $this->fileName);
		}
		// @codeCoverageIgnoreStart
		elseif (!is_readable($this->fileName))
		{
			throw new \InvalidArgumentException('Image file not readable: ' . $this->fileName);
		}
		// @codeCoverageIgnoreEnd
	}
	
	/**
	 * Returns $fileName.
	 *
	 * @see ThumbBase::$fileName
	 */
	public function getFileName ()
	{
		return $this->fileName;
	}
	
	/**
	 * Sets $fileName.
	 *
	 * @param object $fileName
	 * @see ThumbBase::$fileName
	 */
	public function setFileName ($fileName)
	{
		$this->fileName = $fileName;
	}
	
	/**
	 * Returns $format.
	 *
	 * @see ThumbBase::$format
	 */
	public function getFormat ()
	{
		return $this->format;
	}
	
	/**
	 * Sets $format.
	 *
	 * @param object $format
	 * @see ThumbBase::$format
	 */
	public function setFormat ($format)
	{
		$this->format = $format;
	}
	
	public function getIsRemoteImage()
	{
		return $this->remoteImage;
	}
}

<?php

namespace PHPThumb\Tests;

use PHPThumb\GD;

class LoadTest extends \PHPUnit_Framework_TestCase
{
	protected $testImage;
	
	protected function setUp()
	{
		$this->testImage = __DIR__ . '/../resources/test.jpg';
	}
	
	public function testLoadFile()
	{
		$thumb = new GD($this->testImage);
		
		self::assertSame(array('width' => 500, 'height' => 375), $thumb->getCurrentDimensions());
		self::assertSame(array(	'resizeUp' => false,
								'jpegQuality' => 100,
								'correctPermissions' => false,
								'preserveAlpha' => true,
								'alphaMaskColor' => array (	0 => 255,
															1 => 255,
															2 => 255),
								'preserveTransparency' => true,
								'transparencyMaskColor' => array (	0 => 0,
																	1 => 0,
																	2 => 0),
								'interlace' => null), $thumb->getOptions());
		
		
				
		
	}
}
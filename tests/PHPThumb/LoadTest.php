<?php

namespace PHPThumb\Tests;

use PHPThumb\Implementations\GD;

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
	}
}
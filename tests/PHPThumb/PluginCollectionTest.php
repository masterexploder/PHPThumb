<?php

namespace PHPThumb\Tests;

use PHPThumb\ThumbPluginInterface;
use PHPThumb\PluginCollection;

class PluginCollectionTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var PluginCollection
	 */
	protected $pluginCollection;
	
	protected function setUp()
	{
		$this->pluginCollection = new PluginCollection();
	}
	
	public function testState()
	{
		self::assertSame(array(), $this->pluginCollection->getPlugins());
	}
	
	public function testAddPlugin()
	{
		$mockPlugin = $this->getMock('\PHPThumb\ThumbPluginInterface');
		$this->pluginCollection->addPlugin($mockPlugin);
		
		self::assertSame(1, count($this->pluginCollection->getPlugins()));
	}
	
	public function testIterator()
	{
		foreach($this->pluginCollection as $plugin)
		{
			self::assertInstanceOf('\PHPThumb\ThumbPluginInterface', $plugin);
		}
	}
}
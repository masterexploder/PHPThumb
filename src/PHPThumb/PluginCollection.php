<?php

namespace PHPThumb;

class PluginCollection implements \IteratorAggregate
{
    protected $collection = array();

    public function __construct(array $plugins = array())
    {
        $this->collection = $plugins;
    }

    public function addPlugin(ThumbPluginInterface $plugin)
    {
        $this->collection[] = $plugin;
    }

    /**
     * @return ThumbPluginInterface[]
     */
    public function getPlugins()
    {
        return $this->collection;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->collection);
    }
}

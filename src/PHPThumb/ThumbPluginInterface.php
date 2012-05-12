<?php

namespace PHPThumb;

interface ThumbPluginInterface
{
	public function execute(PHPThumb $phpthumb, array $params);
}
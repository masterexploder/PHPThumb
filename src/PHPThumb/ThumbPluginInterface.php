<?php

namespace PHPThumb;

interface ThumbPluginInterface
{
	/**
	 * @param PHPThumb $phpthumb
	 * @return PHPThumb
	 */
	public function execute($phpthumb);
}
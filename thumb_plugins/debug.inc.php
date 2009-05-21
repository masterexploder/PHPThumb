<?php

class PhpThumbDebugFunctions
{
	public function dump(&$that)
	{
		echo '<pre>' . print_r($that, true) . '</pre>';
	}
}

$pt = PhpThumb::getInstance();
$pt->registerPlugin('PhpThumbDebugFunctions', 'gd');
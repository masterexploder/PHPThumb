<?php

require_once 'src/ThumbLib.inc.php';

$cache_path = '/Users/markhuot/Desktop/PHPThumb/cache/';
$cache_url = '/cache/';
$cache = md5(@$_GET['src'].@$_GET['w'].@$_GET['h']);

if (file_exists($cache_path.$cache))
{
	header('Location: '.$cache_url.$cache);
	exit();
}
else
{
	$thumb = PhpThumbFactory::create(@$_GET['src']);
	$thumb->setOptions(array('resizeUp' => true));
	$thumb->adaptiveResize(@$_GET['w'], @$_GET['h']);
	
	$thumb->save($cache_path.$cache);
	
	$thumb->show();
}
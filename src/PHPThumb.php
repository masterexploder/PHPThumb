<?php

$document_path = '/'.trim($_SERVER['DOCUMENT_ROOT'], '/').($_SERVER['DOCUMENT_ROOT']!=''?'/':'');
$document_url = 'http://'.trim($_SERVER['HTTP_HOST'], '/').'/';
$cache_path = '/Users/markhuot/Sites/PHPThumb/cache/';
$cache_uri = 'http://'.trim($_SERVER['HTTP_HOST'], '/').'/cache/';








$params = array('src'=>false, 'w'=>false, 'h'=>false);
$options = array('resizeUp'=>true,'jpegQuality'=>100);
extract(array_merge($params, $options, $_GET));

$src = trim($src, '/');
$cache = md5($src.$w.$h);

require_once 'ThumbLib.inc.php';

if (file_exists($cache_path.$cache))
{
	$thumb = PhpThumbFactory::create($cache_path.$cache);
}
else
{
	if (!file_exists($src))
	{
		$src = $document_path.$src;
	}
	
	$thumb = PhpThumbFactory::create($src);
	$thumb->setOptions($options);
	$thumb->adaptiveResize($w, $h);
	
	$thumb->save($cache_path.$cache);
}

$thumb->show();
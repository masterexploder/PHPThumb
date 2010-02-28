<?php

// SETTINGS

$document_path = '/'.trim($_SERVER['DOCUMENT_ROOT'], '/').($_SERVER['DOCUMENT_ROOT']!=''?'/':'');
$document_url = 'http://'.trim($_SERVER['HTTP_HOST'], '/').'/';
$cache_path = '/Users/markhuot/Sites/PHPThumb/cache/';
$cache_uri = 'http://'.trim($_SERVER['HTTP_HOST'], '/').'/cache/';
$cache_life = '-1 month';

// End configurable settings







$params = array('src'=>false, 'w'=>false, 'h'=>false);
$options = array('resizeUp'=>true,'jpegQuality'=>100,'cache_life'=>$cache_life);
extract(array_merge($params, $options, $_GET));

$src = trim($src, '/');
$cache = md5($src.$w.$h);

require_once 'ThumbLib.inc.php';

if (
	file_exists($cache_path.$cache) &&
	($cache_life == false || filemtime($cache_path.$cache) > strtotime($cache_life)) &&
	@$_SERVER['HTTP_CACHE_CONTROL'] != 'no-cache'
)
{
	saveit('/Users/markhuot/Desktop/memory.log', 'Memory 3: '.number_format(memory_get_usage()/1024, 2).' KiB');
	header('Location: '.$cache_uri.$cache);
	exit();
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

saveit('/Users/markhuot/Desktop/memory.log', 'Memory 1: '.number_format(memory_get_usage()/1024, 2).' KiB');

$thumb->show();
flush();

saveit('/Users/markhuot/Desktop/memory.log', 'Memory 2: '.number_format(memory_get_usage()/1024, 2).' KiB');

































function saveit($filename='', $somecontent="")
{
	if (!$handle = fopen($filename, 'a')) {
		 echo "Cannot open file ($filename)";
		 exit;
	}
	if (fwrite($handle, date('r: ').$somecontent."\n") === FALSE) {
		echo "Cannot write to file ($filename)";
		exit;
	}
	fclose($handle);
}
<?php

$document_path = '/'.trim($_SERVER['DOCUMENT_ROOT'], '/').($_SERVER['DOCUMENT_ROOT']!=''?'/':'');
$document_url = 'http://'.trim($_SERVER['HTTP_HOST'], '/').'/';

$cache_path = '/Users/markhuot/Sites/PHPThumb/cache/';
$cache_uri = 'http://'.trim($_SERVER['HTTP_HOST'], '/').'/cache/';
$cache_total_files = 3000;
$cache_total_size = 1024*100;
$cache_max_age = '+1 month';

$params = array('src'=>false, 'w'=>false, 'h'=>false);
$options = array('resizeUp'=>true,'jpegQuality'=>100);
extract(array_merge($params, $options, $_GET));

$src = trim($src, '/');
$cache = md5($src.$w.$h);

require_once 'ThumbLib.inc.php';

if (false && file_exists($cache_path.$cache) && @$_SERVER['HTTP_CACHE_CONTROL'] != 'no-cache')
{
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

$thumb->show();
flush();





/*
 * Clear Cache
 *
 * Check the cache files and delete extra/old ones.
 */
/*$caches = array();
$dh  = opendir($cache_path);
while (false !== ($filename = readdir($dh))) {
	if (substr($filename, 0, 1) == '.') continue;
	$filemtime = filemtime($cache_path.$filename);
    $caches[$filemtime] = array(
    	'filename' => $filename,
    	'filemtime' => $filemtime
    );
}

$delete = array_slice($caches, $cache_total_files);*/
<?php
/**
 * PhpThumb Library Example File
 * 
 * This file contains example usage for the PHP Thumb Library
 * 
 * PHP Version 5 with GD 2.0+
 * PhpThumb : PHP Thumb Library <http://phpthumb.gxdlabs.com>
 * Copyright (c) 2009, Ian Selby/Gen X Design
 * 
 * Author(s): Mario Rutz <mario@basis42.de>
 * 
 * Licensed under the MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @author Mario Rutz <mario@basis42.de>
 * @copyright Copyright (c) 2010 basis42
 * @link http://www.basis42.de
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version 3.0
 * @package PhpThumb
 * @subpackage Examples
 * @filesource
 */

require_once '../ThumbLib.inc.php';

$thumb = PhpThumbFactory::create('test_2.jpg');
$thumb->adaptiveResize(250, 250)->createTiles(4,4, './tiles');
$thumb->showTiles();

?>

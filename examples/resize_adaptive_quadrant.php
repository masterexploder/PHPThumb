<?php
/**
 * PhpThumb Library Example File
 *
 * This file contains example usage for the PHP Thumb Library
 *
 * PHP Version 5 with GD 2.0+
 * PhpThumb : PHP Thumb Library <http://phpthumb.gxdlabs.com>
 * Copyright (c) 2014, Marcel Domke
 *
 * Author(s): Marcel Domke <contact@marcel-domke.de>
 *
 * Licensed under the MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author Marcel Domke <contact@marcel-domke.de>
 * @copyright Copyright (c) 2014 Marcel Domke
 * @link http://phpthumb.gxdlabs.com
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version 3.0
 * @package PhpThumb
 * @subpackage Examples
 * @filesource
 */

require_once '../vendor/autoload.php';

$thumb = new PHPThumb\GD(__DIR__ .'/../tests/resources/test.jpg');
$thumb->adaptiveResizeQuadrant(300, 300, 'C');

$thumb->show();

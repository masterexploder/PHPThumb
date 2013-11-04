<?php
/**
 * Sharpen Lib Plugin Definition File
 * 
 * This file contains the plugin definition for the Sharpen Lib for PHP Thumb
 * 
 * PHP Version 5 with GD 2.0+
 * PhpThumb : PHP Thumb Library <http://phpthumb.gxdlabs.com>
 * Copyright (c) 2009, Ian Selby/Gen X Design
 * 
 * Author(s): Ian Selby <ian@gen-x-design.com>
 * 
 * Licensed under the MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @author Ian Selby <ian@gen-x-design.com>
 * @copyright Copyright (c) 2009 Gen X Design
 * @link http://phpthumb.gxdlabs.com
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version 3.0
 * @package PhpThumb
 * @filesource
 */


namespace PHPThumb\Plugins;

/**
 * Sharpen Lib Plugin
 * 
 * This plugin allows you to create a sharpened version of your image
 * @author Remi Heens <remi.heens@gmail.com>
 * @package PhpThumb
 * @subpackage Plugins
 */
class Sharpen implements \PHPThumb\PluginInterface
{
    public function execute($phpthumb)
    {
        // sharpen image
        $sharpenMatrix = array (
                        array (-1,-1,-1),
                        array (-1,16,-1),
                        array (-1,-1,-1),
                        );

        $divisor = 8;
        $offset = 0;

        imageconvolution ($phpthumb->getWorkingImage(), $sharpenMatrix, $divisor, $offset);

    }
}
<?php

namespace PHPThumb\Plugins;

    /**
     * GD Trim Lib Plugin Definition File
     *
     * This file contains the plugin definition for the GD Trim Lib for PHP Thumb
     *
     * PHP Version 5.3 with GD 2.0+
     * PhpThumb : PHP Thumb Library <http://phpthumb.gxdlabs.com>
     * Copyright (c) 2009, Ian Selby/Gen X Design
     *
     * Author(s): Ian Selby <ian@gen-x-design.com>
     *
     * Licensed under the MIT License
     * Redistributions of files must retain the above copyright notice.
     *
     * @author Oleg Sherbakov <holdmann@yandex.ru>
     * @copyright Copyright (c) 2016
     * @license http://www.opensource.org/licenses/mit-license.php The MIT License
     * @version 1.0
     * @package PhpThumb
     * @filesource
     */

/**
 * GD Trim Lib Plugin
 *
 * This plugin allows you to trim unnecessary single color borders from any side of image
 *
 * @package PhpThumb
 * @subpackage Plugins
 */
class Trim implements \PHPThumb\PluginInterface
{
    /**
     * @var array Contains trimmed color in array of RGB parts
     */
    protected $color;

    /**
     * @var array Contains array of sides which will be trim
     */
    protected $sides;

    /**
     * Validate whether RGB color parts array valid or not
     *
     * @param $colors
     * @return bool
     */
    private function validateColor($colors)
    {
        if (!(is_array($colors) && count($colors) == 3))
            return false;

        foreach($colors as $color) {
            if ($color < 0 || $color > 255)
                return false;
        }

        return true;
    }

    /**
     * Validates whether sides is valid or not
     *
     * @param $sidesString
     * @return bool
     */
    private function validateSides($sidesString)
    {
        $sides = str_split($sidesString);

        if (count($sides) > 4 || count($sides) == 0)
            return false;

        foreach($sides as $side) {
            if (!in_array($side, array('T', 'B', 'L', 'R')))
                return false;
        }

        return true;
    }

    /**
     * Trim constructor
     *
     * @param array $color
     * @param string $sides
     */
    public function __construct($color = array(255, 255, 255), $sides = 'TBLR')
    {
        // make sure our arguments are valid
        if (!$this->validateColor($color)) {
            throw new \InvalidArgumentException('Color must be array of RGB color model parts');
        }

        if (!$this->validateSides($sides)) {
            throw new \InvalidArgumentException('Sides must be string with T, B, L, and/or R coordinates');
        }

        $this->color    = $color;
        $this->sides    = str_split($sides);
    }

    /**
     * Converts rgb parts array to integer representation
     *
     * @param array $rgb
     * @return number
     */
    private function rgb2int(array $rgb)
    {
        return hexdec(
            sprintf("%02x%02x%02x", $rgb[0], $rgb[1], $rgb[2])
        );
    }

    /**
     * @param \PHPThumb\GD $phpthumb
     * @return \PHPThumb\GD
     */
    public function execute($phpthumb)
    {
        $currentImage = $phpthumb->getOldImage();
        $currentDimensions = $phpthumb->getCurrentDimensions();

        $borderTop = 0;
        $borderBottom = 0;
        $borderLeft = 0;
        $borderRight = 0;

        if (in_array('T', $this->sides)) {
            for (; $borderTop < $currentDimensions['height']; ++$borderTop) {
                for ($x = 0; $x < $currentDimensions['width']; ++$x) {
                    if (imagecolorat(
                            $currentImage,
                            $x,
                            $borderTop
                        ) != $this->rgb2int($this->color)) {
                        break 2;
                    }
                }
            }
        }

        if (in_array('B', $this->sides)) {
            for (; $borderBottom < $currentDimensions['height']; ++$borderBottom) {
                for ($x = 0; $x < $currentDimensions['width']; ++$x) {
                    if (imagecolorat(
                            $currentImage,
                            $x,
                            $currentDimensions['height'] - $borderBottom - 1
                        ) != $this->rgb2int($this->color)) {
                        break 2;
                    }
                }
            }
        }

        if (in_array('L', $this->sides)) {
            for (; $borderLeft < $currentDimensions['width']; ++$borderLeft) {
                for ($y = 0; $y < $currentDimensions['height']; ++$y) {
                    if (imagecolorat(
                            $currentImage,
                            $borderLeft,
                            $y
                        ) != $this->rgb2int($this->color)) {
                        break 2;
                    }
                }
            }
        }

        if (in_array('R', $this->sides)) {
            for (; $borderRight < $currentDimensions['width']; ++$borderRight) {
                for ($y = 0; $y < $currentDimensions['height']; ++$y) {
                    if (imagecolorat(
                            $currentImage,
                            $currentDimensions['width'] - $borderRight - 1,
                            $y
                        ) != $this->rgb2int($this->color)) {
                        break 2;
                    }
                }
            }
        }

        $newWidth = $currentDimensions['width'] - ($borderLeft + $borderRight);
        $newHeight = $currentDimensions['height'] - ($borderTop + $borderBottom);

        $newImage = imagecreatetruecolor(
            $newWidth,
            $newHeight
        );

        imagecopy(
            $newImage,
            $currentImage,
            0,
            0,
            $borderLeft,
            $borderTop,
            $currentDimensions['width'],
            $currentDimensions['height']
        );

        $phpthumb->setOldImage($newImage);

        $phpthumb->setCurrentDimensions(array(
            'width' => $newWidth,
            'height' => $newHeight
        ));

        return $phpthumb;
    }
}



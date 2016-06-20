<?php

namespace PHPThumb\Plugins;

    /**
     * GD Watermark Lib Plugin Definition File
     *
     * This file contains the plugin definition for the GD Watermark Lib for PHP Thumb
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
 * GD Watermark Lib Plugin
 *
 * This plugin allows you to add watermark above the image
 *
 * @package PhpThumb
 * @subpackage Plugins
 */
class Watermark implements \PHPThumb\PluginInterface
{
    protected $wm;
    protected $position;
    protected $opacity;
    protected $offsetX;
    protected $offsetY;

    /**
     * Watermark constructor.
     *
     * @param \PHPThumb\GD $wm Watermark image as \PHPThumb\GD instance
     * @param string $position Can be: left/west, right/east, center for the x-axis and top/north/upper, bottom/lower/south, center for the y-axis
     * @param int $opacity Opacity of the watermark in percent, 0 = total transparent, 100 = total opaque
     * @param int $offsetX Offset on the x-axis. can be negative to set an offset to the left
     * @param int $offsetY Offset on the y-axis. can be negative to set an offset to the top
     */
    public function __construct(\PHPThumb\GD $wm, $position = 'center', $opacity = 100, $offsetX = 0, $offsetY = 0)
    {
        $this->wm       = $wm;
        $this->position = $position;
        $this->opacity  = $opacity;
        $this->offsetX  = $offsetX;
        $this->offsetY  = $offsetY;
    }

    /**
     * @param \PHPThumb\GD $phpthumb
     * @return \PHPThumb\GD
     */
    public function execute($phpthumb)
    {
        $currentDimensions = $phpthumb->getCurrentDimensions();
        $watermarkDimensions = $this->wm->getCurrentDimensions();

        $watermarkPositionX = $this->offsetX;
        $watermarkPositionY = $this->offsetY;

        if (preg_match('/right|east/i', $this->position)) {
            $watermarkPositionX += $currentDimensions['width'] - $watermarkDimensions['width'];
        } else if (!preg_match('/left|west/i', $this->position)) {
            $watermarkPositionX += intval($currentDimensions['width']/2 - $watermarkDimensions['width']/2);
        }

        if (preg_match('/bottom|lower|south/i', $this->position)) {
            $watermarkPositionY += $currentDimensions['height'] - $watermarkDimensions['height'];
        } else if (!preg_match('/upper|top|north/i', $this->position)) {
            $watermarkPositionY += intval($currentDimensions['height']/2 - $watermarkDimensions['height']/2);
        }

        $workingImage = $phpthumb->getWorkingImage();
        $watermarkImage = ($this->wm->getWorkingImage() ? $this->wm->getWorkingImage() : $this->wm->getOldImage());

        $this->imageCopyMergeAlpha(
            $workingImage,
            $watermarkImage,
            $watermarkPositionX,
            $watermarkPositionY,
            0,
            0,
            $watermarkDimensions['width'],
            $watermarkDimensions['height'],
            $this->opacity
        );

        $phpthumb->setWorkingImage($workingImage);

        return $phpthumb;
    }

    /**
     * Function copied from: http://www.php.net/manual/en/function.imagecopymerge.php#92787
     * Does the same as "imagecopymerge" but preserves the alpha-channel
     */
    private function imageCopyMergeAlpha(&$dst_im, &$src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){
        $cut = imagecreatetruecolor($src_w, $src_h);
        imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);
        imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
        imagecopymerge($dst_im, $cut, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct);
    }
}

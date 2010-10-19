<?php

/**
 * Jpg Image Rotation Plugin
 * 
 * This plugin manipulate jpg rotation depends on the EXIF headers from JPEG or TIFF files
 * http://php.net/manual/en/function.exif-read-data.php
 * Thanks mafo at mafo removethis dot sk for his code example
 * 
 * PHP Version 5 with GD 2.0+
 *
 * @package PhpThumb 
 * @version 1.0
 * @copyright AFTDesign
 * @author Nikita E. Korotkih <nikitez@bk.ru>
 * @license PHP Version 5.0
 */
class JpgImageRotation
{
	/**
	 * Instance of GdThumb passed to this class
	 * 
	 * @var GdThumb
	 */
	protected $parentInstance;
		
	public function rotateJpg (&$that)
	{
        $this->parentInstance = $that;
        $exif = exif_read_data($this->parentInstance->getFilename());
        
        switch($exif['Orientation'])
        {
            case 1: // nothing
            break;

            case 2:
                //code here
            break;

            case 3: // 180 rotate left
                $this->parentInstance->rotateImageNDegrees(180);
            break;
            
            case 4: // vertical flip
                //
            break;

            case 5: // vertical flip + 90 rotate right
                //
            break;

            case 6: // 90 rotate right
                $this->parentInstance->rotateImageNDegrees(-90);
            break;

            case 7: // horizontal flip + 90 rotate right
                //
            break;

            case 8:    // 90 rotate left
                $this->parentInstance->rotateImageNDegrees(90);
            break;
        }


        return $that;
    }

}

$pt = PhpThumb::getInstance();
$pt->registerPlugin('JpgImageRotation', 'gd');

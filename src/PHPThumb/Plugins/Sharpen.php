<?php

namespace PHPThumb\Plugins;

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
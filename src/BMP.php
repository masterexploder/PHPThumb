<?php
// Read 1,4,8,24,32bit BMP files 
// Save 24bit BMP files

// Author: de77
// Licence: MIT
// Webpage: de77.com
// Article about this class: http://de77.com/php/read-and-write-bmp-in-php-imagecreatefrombmp-imagebmp
// First-version: 07.02.2010
// Version: 21.08.2010

class BMP
{
	public static function imagebmp(&$img, $filename = false)
	{
		$wid = imagesx($img);
		$hei = imagesy($img);
		$wid_pad = str_pad('', $wid % 4, "\0");
		
		$size = 54 + ($wid + $wid_pad) * $hei * 3; //fixed
		
		//prepare & save header
		$header['identifier']		= 'BM';
		$header['file_size']		= self::dword($size);
		$header['reserved']			= self::dword(0);
		$header['bitmap_data']		= self::dword(54);
		$header['header_size']		= self::dword(40);
		$header['width']			= self::dword($wid);
		$header['height']			= self::dword($hei);
		$header['planes']			= self::word(1);
		$header['bits_per_pixel']	= self::word(24);
		$header['compression']		= self::dword(0);
		$header['data_size']		= self::dword(0);
		$header['h_resolution']		= self::dword(0);
		$header['v_resolution']		= self::dword(0);
		$header['colors']			= self::dword(0);
		$header['important_colors']	= self::dword(0);
	
		if ($filename)
		{
		    $f = fopen($filename, "wb");
		    foreach ($header AS $h)
		    {
		    	fwrite($f, $h);
		    }
		    
			//save pixels
			for ($y=$hei-1; $y>=0; $y--)
			{
				for ($x=0; $x<$wid; $x++)
				{
					$rgb = imagecolorat($img, $x, $y);
					fwrite($f, byte3($rgb));
				}
				fwrite($f, $wid_pad);
			}
			fclose($f);
		}
		else
		{
		    foreach ($header AS $h)
		    {
		    	echo $h;
		    }
		    
			//save pixels
			for ($y=$hei-1; $y>=0; $y--)
			{
				for ($x=0; $x<$wid; $x++)
				{
					$rgb = imagecolorat($img, $x, $y);
					echo self::byte3($rgb);
				}
				echo $wid_pad;
			}
		}	
	}
	
	public static function imagecreatefrombmp($filename)
	{
		$f = fopen($filename, "rb");

		//read header    
	    $header = fread($f, 54);
	    $header = unpack(	'c2identifier/Vfile_size/Vreserved/Vbitmap_data/Vheader_size/' .
							'Vwidth/Vheight/vplanes/vbits_per_pixel/Vcompression/Vdata_size/'.
							'Vh_resolution/Vv_resolution/Vcolors/Vimportant_colors', $header);
	
	    if ($header['identifier1'] != 66 or $header['identifier2'] != 77)
	    {
	    	die('Not a valid bmp file');
	    }
	    
	    if (!in_array($header['bits_per_pixel'], array(24, 32, 8, 4, 1)))
	    {
	    	die('Only 1, 4, 8, 24 and 32 bit BMP images are supported');
	    }
	    
		$bps = $header['bits_per_pixel']; //bits per pixel 
	    $wid2 = ceil(($bps/8 * $header['width']) / 4) * 4;
		$colors = pow(2, $bps);
	
	    $wid = $header['width'];
	    $hei = $header['height'];
	
	    $img = imagecreatetruecolor($header['width'], $header['height']);
	
		//read palette
		if ($bps < 9)
		{
			for ($i=0; $i<$colors; $i++)
			{
				$palette[] = self::undword(fread($f, 4));
			}
		}
		else
		{
			if ($bps == 32)
			{
				imagealphablending($img, false);
				imagesavealpha($img, true);			
			}
			$palette = array();
		}	
	
		//read pixels    
	    for ($y=$hei-1; $y>=0; $y--)
	    {
			$row = fread($f, $wid2);		
			$pixels = self::str_split2($row, $bps, $palette);
	    	for ($x=0; $x<$wid; $x++)
	    	{
	    		self::makepixel($img, $x, $y, $pixels[$x], $bps);
	    	}
	    }
		fclose($f);    	    
		
		return $img;
	}
	
	private static function str_split2($row, $bps, $palette)
	{
		switch ($bps)
		{
			case 32:
			case 24:	return str_split($row, $bps/8);
			case  8:	$out = array();
						$count = strlen($row);				
						for ($i=0; $i<$count; $i++)
						{					
							$out[] = $palette[	ord($row[$i])		];
						}				
						return $out;		
			case  4:	$out = array();
						$count = strlen($row);				
						for ($i=0; $i<$count; $i++)
						{
							$roww = ord($row[$i]);						
							$out[] = $palette[	($roww & 240) >> 4	];
							$out[] = $palette[	($roww & 15) 		];
						}				
						return $out;
			case  1:	$out = array();
						$count = strlen($row);				
						for ($i=0; $i<$count; $i++)
						{
							$roww = ord($row[$i]);						
							$out[] = $palette[	($roww & 128) >> 7	];
							$out[] = $palette[	($roww & 64) >> 6	];
							$out[] = $palette[	($roww & 32) >> 5	];
							$out[] = $palette[	($roww & 16) >> 4	];
							$out[] = $palette[	($roww & 8) >> 3	];
							$out[] = $palette[	($roww & 4) >> 2	];
							$out[] = $palette[	($roww & 2) >> 1	];
							$out[] = $palette[	($roww & 1)			];
						}				
						return $out;					
		}
	}
	
	private static function makepixel($img, $x, $y, $str, $bps)
	{
		switch ($bps)
		{
			case 32 :	$a = ord($str[0]);
						$b = ord($str[1]);
						$c = ord($str[2]);
						$d = 256 - ord($str[3]); //TODO: gives imperfect results
						$pixel = $d*256*256*256 + $c*256*256 + $b*256 + $a;
						imagesetpixel($img, $x, $y, $pixel);
						break;
			case 24 :	$a = ord($str[0]);
						$b = ord($str[1]);
						$c = ord($str[2]);
						$pixel = $c*256*256 + $b*256 + $a;
						imagesetpixel($img, $x, $y, $pixel);
						break;					
			case 8 :
			case 4 :
			case 1 :	imagesetpixel($img, $x, $y, $str);
						break;
		}
	}
	
	private static function byte3($n)
	{
		return chr($n & 255) . chr(($n >> 8) & 255) . chr(($n >> 16) & 255);	
	}
	
	private static function undword($n)
	{
		$r = unpack("V", $n);
		return $r[1];
	}
	
	private static function dword($n)
	{
		return pack("V", $n);
	}
	
	private static function word($n)
	{
		return pack("v", $n);
	}
}

function imagebmp(&$img, $filename = false)
{
	return BMP::imagebmp($img, $filename);
}

function imagecreatefrombmp($filename)
{
	return BMP::imagecreatefrombmp($filename);    
}	
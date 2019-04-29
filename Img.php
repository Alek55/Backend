<?php

	namespace app\lib;
	class IMG {
		
		public static function img_resize($dir_file, $height_, $width_, $dir_res_file, $file_name, $type, $height=false) {
			switch($type) {
				case "jpeg":
				case "pjpeg":
					$img_id = imagecreatefromjpeg($dir_file.$file_name);
				break;
				case "png":
				case "x-png":
					$img_id = imagecreatefrompng($dir_file.$file_name);
				break;
				case "gif":
					$img_id = imagecreatefromgif($dir_file.$file_name);
				break;
			}
			
			$img_width = imageSX($img_id);
			$img_height = imageSY($img_id);
			
			if($img_height < $img_width) $height = true;
			
			if($height) $k = round($img_height/$height_, 2);
			else $k = round($img_width/$width_, 2);
			$img_mini_width = round($img_width/$k);
			$img_mini_height = round($img_height/$k);
			
			$img_dest_id = imagecreatetruecolor($img_mini_width, $img_mini_height);
			imagecopyresampled($img_dest_id, $img_id, 0, 0, 0, 0, $img_mini_width, $img_mini_height, $img_width, $img_height);
			
			$equally = false;
			
			if($img_mini_height < $img_mini_width) {
				$x_0 = floor(($img_mini_width - $width_) / 2);
				$y_0 = 0;
			}
			elseif($img_mini_height > $img_mini_width) {
				$x_0 = 0;
				$y_0 = 0;
			}
			else $equally = true;
			
			switch($type) {
				case "jpeg":
				case "pjpeg":
					$img = imagejpeg($img_dest_id, $dir_res_file.$file_name, 100);
				break;
				case "png":
				case "x-png":
					$img = imagepng($img_dest_id, $dir_res_file.$file_name);
				break;
				case "gif":
					$img = imagegif($img_dest_id, $dir_res_file.$file_name);
				break;
			}
			imagedestroy($img_id);
			imagedestroy($img_dest_id);
			
			if($img) {
				if(!$equally) {
					$imG =  self::img_cut_out($dir_res_file.$file_name, $x_0, $y_0, $width_, $height_);
					if($imG) return true;
					else return false;
				}
				else return true;
			}
			else return false;
		}
		
		public static function img_cut_out($image, $x_0, $y_0, $w_0, $h_0) {
			if (($x_0 < 0) || ($y_0 < 0) || ($w_0 < 0) || ($h_0 < 0)) return false;
			list($w_i, $h_1, $type) = getimagesize($image);
			$types = array("", "gif", "jpeg", "png");
			$ect = $types[$type];
			if($ect) {
				$func = "imagecreatefrom".$ect;
				$img_i = $func($image);
			}
			else return false;
			$img_0 = imagecreatetruecolor($w_0, $h_0);
			imagecopy($img_0, $img_i, 0, 0, $x_0, $y_0, $w_0, $h_0);
			$func = "image".$ect;
			return $func($img_0, $image);
		}
		
		public static function getWater($path_img) {
			$main_img = getimagesize($path_img);
			if(!$main_img) return false;
			$func = "imagecreatefrom".substr($main_img["mime"], strpos("/", $main_img["mime"])+1);
			$img = $func($path_img);
			if(file_exists(Config::WM)) $water = imagecreatefrompng(Config::WM);
			$res_width = $main_img[0];
			$res_height = $main_img[1];
			
			$water_width = imagesx($water);
			$water_height = imagesy($water);
			
			$res_img = imagecreatetruecolor($res_width, $res_height);
			imagecopyresampled($res_img, $img, 0, 0, 0, 0, $res_width, $res_height, $res_width, $res_height);
			imagecopy($res_img, $water, $res_width-$water_width, $res_height-$water_height, 0, 0, $water_width, $water_height);
			return imagejpeg($res_img, $path_img, 100);
		}
		
	}
?>
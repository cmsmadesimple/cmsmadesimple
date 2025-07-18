<?php
# FileManager. A plugin for CMS - CMS Made Simple
# Copyright (c) 2006-08 by Morten Poulsen <morten@poulsen.org>
#
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#Visit our homepage at: http://www.cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#

/**
 * Public utility class used to manipulate instances of images.
 */
final class imageEditor
{
	private function __construct() {}

	/**
	 * process a resize on a instance of image
	 *
	 * @param image the instance of image
	 * @param mimeType the mimetype of the image
	 * @param image_width the new width
	 * @param image_height the new height
	 *
	 * @return image instance of the image resized
	 **/
	public static function resize($image, $mimeType, $image_width, $image_height){

		$newImage = @imagecreatetruecolor($image_width, $image_height);
		if ($mimeType && ($mimeType == image_type_to_mime_type(IMAGETYPE_GIF) || $mimeType == image_type_to_mime_type(IMAGETYPE_PNG))) {
			//Keep transparency
			imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
			imagealphablending($newImage, false);
			imagesavealpha($newImage, true);
		}

		imagecopyresampled($newImage, $image, 0, 0, 0, 0, $image_width, $image_height, imagesx($image), imagesy($image));
		return $newImage;
	}

	/**
	 * process a crop on a instance of image
	 *
	 * @param image the instance of image
	 * @param mimeType the mimetype of the image
	 * @param crop_x the x position to begin the crop (top-left)
	 * @param crop_y the y position to begin the crop (top-left)
	 * @param crop_width the width to end the crop (from the left to the right)
	 * @param crop_height the height to end the crop (from the top to the bottom)
	 *
	 * @return image instance of the image cropped
	 **/
	public static function crop($image, $mimeType, $crop_x, $crop_y, $crop_width, $crop_height){

		$newImage = @imagecreatetruecolor($crop_width, $crop_height);
		if ($mimeType && ($mimeType == image_type_to_mime_type(IMAGETYPE_GIF) || $mimeType == image_type_to_mime_type(IMAGETYPE_PNG))) {
			//Keep transparency
			imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
			imagealphablending($newImage, false);
			imagesavealpha($newImage, true);
		}

		imagecopyresampled($newImage, $image, 0, 0, $crop_x, $crop_y, $crop_width, $crop_height, $crop_width, $crop_height);
		return $newImage;
	}

	/**
	 * return the mimetype of a file
	 *
	 * @param path the path of the file
	 *
	 * @return mime the mimetype of the file
	 **/
	public static function getMime($path){
		$info = getimagesize($path);
		if (!$info) {
			return false;
		}
		$mime = image_type_to_mime_type($info[2]);
		switch ($mime) {
			case 'image/jpeg':
			case 'image/gif':
			case 'image/png':
			case 'image/webp':
			case 'image/avif':
			return $mime;
		default:
			return false;
		}
	}

	/**
	 * return the width of a file
	 *
	 * @param path the path of the file
	 *
	 * @return int the width of the file
	 **/
	public static function getWidth($path){
		$info = getimagesize($path);
		if (!$info) {
			return false;
		}
		return $info[0];
	}

	/**
	 * Will load the file $path and return an instance of image
	 *
	 * @param path the path of the file
	 *
	 * @return image instance of the image
	 **/
	public static function open($path) {

		$mimeType = imageEditor::getMime($path);
		if (!$mimeType){
			return "INVALID IMAGE TYPE";
		}

		switch ($mimeType) {
			case 'image/jpeg':
				return imagecreatefromjpeg($path);
			case 'image/gif':
				return imagecreatefromgif($path);
			case 'image/png':
				return imagecreatefrompng($path);
			case 'image/webp':
				return imagecreatefromwebp($path);
			case 'image/avif':
				if (PHP_VERSION_ID >= 80000 && function_exists('imagecreatefromavif')) {
					return imagecreatefromavif($path);
				} else return NULL;
			default:
				return NULL;
	}

	/**
	 * Will save the instance of $image into the file $path
	 *
	 * @param image instance of the image
	 * @param path the path of the file
	 * @param mimeType the mimetype of the image
     * @return bool
	 **/
	public static function save($image, $path, $mimeType){
		switch ($mimeType) {
			case 'image/jpeg':
				return imagejpeg($image, $path);
			case 'image/gif':
				return imagegif($image, $path);
			case 'image/png':
				imagesavealpha($image, true); //TODO also for webp and avif
				return imagepng($image, $path);
			case 'image/webp':
				return imagewebp($image, $path);
			case 'image/avif':
				if (PHP_VERSION_ID >= 80000 && function_exists('imageavif')) {
					return imageavif($image, $path);
				} else return false;
			default:
				return false;
		}
	}
}
?>

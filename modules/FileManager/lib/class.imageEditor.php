<?php

final class imageEditor 
{
	public function __construct() {}
  
	public function resize($image, $mimeType, $image_width, $image_height){
  
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
  
	public function crop($image, $mimeType, $crop_x, $crop_y, $crop_width, $crop_height){

		$newImage = @imagecreatetruecolor($crop_width, $crop_height);
		if ($mimeType && ($mimeType == image_type_to_mime_type(IMAGETYPE_GIF) || $mimeType == image_type_to_mime_type(IMAGETYPE_PNG))) { 
			//Keep transparency
			imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
			imagealphablending($newImage, false);
			imagesavealpha($newImage, true);
		}
				
		//imagecopyresampled($newImage, $image, 0, 0, $cropLeft, $cropTop, $crop_width, $crop_height, $crop_width, $crop_height);
		imagecopyresampled($newImage, $image, 0, 0, $crop_x, $crop_y, $crop_width, $crop_height, $crop_width, $crop_height);
		return $newImage;
	}
  
	public function getMime($path){
		$info = getimagesize($path);
		if (!$info) {
			return false;
		}
		$mime = image_type_to_mime_type($info[2]);
		if($mime != image_type_to_mime_type(IMAGETYPE_JPEG) 
			&& $mime != image_type_to_mime_type(IMAGETYPE_GIF) 
			&& $mime != image_type_to_mime_type(IMAGETYPE_PNG)){
			return false;
		}
		return $mime;
	}

	public function getWidth($path){
		$info = getimagesize($path);
		if (!$info) {
			return false;
		}
		return $info[0];
	}
  
	public function open($path, $pathTemp = null) {
		
		$mimeType = $this->getMime($path);

		if (!$mimeType){
			return "INVALID IMAGE TYPE";
		}
		/*
		if($pathTemp != null){
			$dirTemp = dirname($pathTemp);
			if(!is_dir($dirTemp) && !mkdir ($dirTemp, 0775, true)){
				return "COPY FAILED";
			}
			
			if(!copy($path, $pathTemp)){
				return "COPY FAILED";
			}
			$path = $pathTemp;
		}*/
				
		if ($mimeType == image_type_to_mime_type(IMAGETYPE_JPEG)) {	
			return imagecreatefromjpeg($path);
		}
		else if ($mimeType == image_type_to_mime_type(IMAGETYPE_GIF)) {	
			return imagecreatefromgif($path);
		}
		else if ($mimeType == image_type_to_mime_type(IMAGETYPE_PNG)) {
			return imagecreatefrompng($path);
		}
		
		return NULL;
	}
	
	function save($image, $path, $mimeType){
	/*
		if($path != null){
						
			if(!copy($pathTemp, $path)){
				return "COPY FAILED";
			}
			$pathTemp = $path;
		}*/
		if ($mimeType == image_type_to_mime_type(IMAGETYPE_JPEG)) {	
			imagejpeg($image, $path);		
		} else if ($mimeType == image_type_to_mime_type(IMAGETYPE_GIF)) {	
			imagegif($image, $path);		
		} else if ($mimeType == image_type_to_mime_type(IMAGETYPE_PNG)) {
			imagesavealpha($image, true);
			imagepng($image, $path);		
		}
	}
}
?>

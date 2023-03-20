<?php
namespace AppBundle;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Intervention\Image\Image;

class FileUploader
{

// @fill
// center       - Centers the image without any scaling.  May be smaller than dimensions, or cropped.
// stretch      - Stretches the image to fill dimensions.  May change image proportions.
// squeeze      - Scales the image, maintaining original proportions.  May not fill dimensions.
// trim         - Scales the image, maintaining original proportions, to fill dimensions.  Image may be centered and trimmed.
// trim_rand    - Scales the image, maintaining original proportions, to fill dimensions.  Trimmed randomly.

public function resize_with_ext($src_filename, $src_ext, $dst_filename, $dst_width, $dst_height, $fill='squeeze', $quality=80, $png_filters=PNG_NO_FILTER) {
    if(!file_exists($src_filename)) {
        //throw new Exception("File does not exist: $src_filename");
        return false;
    }
    if(empty($dst_filename)) {
        $dst_filename = $src_filename;
    }
    if($dst_width <= 0) {
        //throw new Exception("Width must be positive: $dst_width");
        return false;
    }
    if($dst_height <= 0) {
        //throw new Exception("Height must be positive: $dst_height");
        return false;
    }
    
//  ini_set("memory_limit","400M");
    
    switch(strtolower($src_ext)) {
        case 'gif':
            $src_image = @imagecreatefromgif($src_filename);
            break;
        case 'jpe':
        case 'jpeg':
        case 'jpg':            
            $src_image = @imagecreatefromjpeg($src_filename);
           
            break;
        case 'png':
            $src_image = @imagecreatefrompng($src_filename);
            break;
        default:
            //throw new Exception("Invalid source file extension: $src_ext");
            return false;
    }
    
    if($src_image === FALSE) {
    	move_uploaded_file($src_filename, $dst_filename);
    	return;
    }
    
    $exif = @exif_read_data($src_filename);
    if(!empty($exif['Orientation'])) {
    	switch($exif['Orientation']) {
    		case 8:
    			$src_image = imagerotate($src_image,90,0);
    			break;
    		case 3:
    			$src_image = imagerotate($src_image,180,0);
    			break;
    		case 6:
    			$src_image = imagerotate($src_image,-90,0);
    			break;
    	}
    }

    $src_width = imagesx($src_image);
    $src_height = imagesy($src_image);
    switch(strtolower(trim($fill))) {
        case 'center':
            $src_x = round($src_width/2-$dst_width/2);
            $src_y = round($src_height/2-$dst_height/2);
            if($src_x < 0) {
                $dst_width = $src_width;
                $src_x = 0;
            }
            if($src_y < 0) {
                $dst_height = $src_height;
                $src_y = 0;
            }
            $dst_image = imagecreatetruecolor($dst_width, $dst_height);
            imagecopyresampled($dst_image, $src_image, 0, 0, $src_x, $src_y, $dst_width, $dst_height, $dst_width, $dst_height);
            break;
        case 'stretch':
            $dst_image = imagecreatetruecolor($dst_width, $dst_height);
            imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
            break;
        case 'crop':
        case 'crop_center':
        case 'trim_center':
        case 'trim':
            $src_ratio = $src_width/$src_height;
            $dst_ratio = $dst_width/$dst_height;
            if($src_ratio < $dst_ratio) // trim top and bottom
            {
                $ratio = $src_width/$dst_width;
                $crop_height = $dst_height*$ratio;
                $src_y = round(($src_height-$crop_height)/2);
                $crop_width = $src_width;
                $src_x = 0;
            }
            else // trim left and right
            {
                $ratio = $src_height/$dst_height;
                $crop_width = $dst_width*$ratio;
                $src_x = round(($src_width-$crop_width)/2);
                $crop_height = $src_height;
                $src_y = 0;
            }
            $dst_image = imagecreatetruecolor($dst_width, $dst_height);
            imagecopyresampled($dst_image, $src_image, 0, 0, $src_x, $src_y, $dst_width, $dst_height, $crop_width, $crop_height);
            break;
        case 'trim_top':
            $src_ratio = $src_width/$src_height;
            $dst_ratio = $dst_width/$dst_height;
            if($src_ratio < $dst_ratio) // trim top and bottom
            {
                $ratio = $src_width/$dst_width;
                $crop_height = $dst_height*$ratio;
                $src_y = 0;
                $crop_width = $src_width;
                $src_x = 0;
            }
            else // trim left and right
            {
                $ratio = $src_height/$dst_height;
                $crop_width = $dst_width*$ratio;
                $src_x = round(($src_width-$crop_width)/2);
                $crop_height = $src_height;
                $src_y = 0;
            }
            $dst_image = imagecreatetruecolor($dst_width, $dst_height);
            imagecopyresampled($dst_image, $src_image, 0, 0, $src_x, $src_y, $dst_width, $dst_height, $crop_width, $crop_height);
            break;
        case 'crop_rand':
        case 'trim_rand':
            $src_ratio = $src_width/$src_height;
            $dst_ratio = $dst_width/$dst_height;
            if($src_ratio < $dst_ratio) // trim top and bottom
            {
                $ratio = $src_width/$dst_width;
                $crop_height = $dst_height*$ratio;
                $src_y = rand(0,$src_height-$crop_height);
                $crop_width = $src_width;
                $src_x = 0;
            }
            else // trim left and right
            {
                $ratio = $src_height/$dst_height;
                $crop_width = $dst_width*$ratio;
                $src_x = rand(0,$src_width-$crop_width);
                $crop_height = $src_height;
                $src_y = 0;
            }
            $dst_image = imagecreatetruecolor($dst_width, $dst_height);
            imagecopyresampled($dst_image, $src_image, 0, 0, $src_x, $src_y, $dst_width, $dst_height, $crop_width, $crop_height);
            break;
        case 'squeeze':
        case 'stretch_prop':
        case 'fit':
            $ratio = max($src_width/$dst_width, $src_height/$dst_height);
            if($ratio < 1) $ratio = 1; // do not enlarge
            $dst_width = round($src_width/$ratio);
            $dst_height = round($src_height/$ratio);
            $dst_image = imagecreatetruecolor($dst_width, $dst_height);
            imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);
            break;
        default:
            //throw new Exception("Unrecognized fill type: $fill");
            return false;
    }
    $dst_ext = substr($dst_filename,strrpos($dst_filename,'.')+1);
    if(empty($dst_ext)) {
        $dst_ext = $src_ext;
        $dst_filename .= ".$src_ext";
    }
    switch(strtolower($dst_ext)) {
        case 'gif':
            return imagegif($dst_image, $dst_filename);
        case 'jpe':
        case 'jpeg':
        case 'jpg':
            return imagejpeg($dst_image, $dst_filename, $quality);
        case 'png':
            return imagepng($dst_image, $dst_filename, (int)($quality / 10), $png_filters);
        default:
            //throw new Exception('Invalid destination file extension: $dst_ext');
            return false;
    }
}


public function resize($src_filename, $dst_filename, $dst_width, $dst_height, $fill='squeeze', $quality=80, $png_filters=PNG_NO_FILTER)
{
    if(!file_exists($src_filename)) {
        //throw new Exception("File does not exist: $src_filename");
        return false;
    }
	$src_ext = substr($src_filename,strrpos($src_filename,'.')+1);
    $this->resize_with_ext($src_filename, $src_ext, $dst_filename, $dst_width, $dst_height, $fill, $quality, $png_filters);
}

    public function resizeImage($filename, $width, $height, $method = 'fit')
    {
    	$extension = substr($filename, strrpos($filename, ".") + 1);

    	$file = tmpfile();
		$path = stream_get_meta_data($file)['uri'];
		fclose($file);

    	copy($filename, $path);

    	$this->resize_with_ext($path, $extension, $filename, $width, $height, $method);
    	unlink($path);
    }

    public function uploadAndResize(UploadedFile $file, $targetDir, $width, $height, $method = 'trim_top')
    {
    	$extension = $file->guessExtension();
        $fileName = md5(uniqid()) . '.' . $extension;
        $file->move($targetDir, $fileName);

        $this->resize_with_ext($targetDir . "/" . $fileName, $extension, $targetDir . "/" . $fileName, $width, $height, $method);

/*
        $img = Image::make($targetDir . $fileName);

        $img->fit($width, $height, function ($constraint) {
    		$constraint->upsize();
		}, 'top');

		$img->save($targetDir . $fileName);
*/
        return $fileName;
    }

    public function generateFileName($targetDir, $extension) {
        $fileName = md5(uniqid()) . '.' . $extension;
    	return $fileName;
    }

    public function upload(UploadedFile $file, $targetDir)
    {
    	$extension = $file->guessExtension();
    	if(strcmp($extension, "mp4a") == 0) {
    		$extension = "mp4";
    	}

        $fileName = md5(uniqid()) . '.' . $extension;
        $file->move($targetDir, $fileName);
        return $fileName;
    }
}
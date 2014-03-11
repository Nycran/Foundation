<?php
namespace Myndie\Lib;

define("MYNDIE_IMAGE_RESIZE_EXACT", 1);
define("MYNDIE_IMAGE_RESIZE_MAX_WIDTH", 2);
define("MYNDIE_IMAGE_RESIZE_MAX_HEIGHT", 3);

class ImageModify
{
	private static $extension;
	private static $image;
	private static $newImage;
	private static $originalWidth;
	private static $originalHeight;
	private static $newWidth;
	private static $newHeight;
    
    /**
    * Resizes an image to an exact height and width and creates the specified output image.
    * Optionally, the image can also be downloaded.
    * 
    * @param string $sourceImage  The path to the source image
    * @param string $destPath The path to the destination image
    * @param integer $width The width to resize the image to
    * @param integer $height The height to resize the image to
    * @param mixed $imageQuality The desired output quality (0-100)
    * @param boolean $download Set to true to trigger a download
    */
    public static function resizeToExactHeightAndWidthAndSave($sourceImage, $destPath, $width, $height, $imageQuality = 100, $download = false)
    {
        // Load the source image
        self::getImage($sourceImage);
        
        // Resize it
        self::resizeTo($width, $height, MYNDIE_IMAGE_RESIZE_EXACT);
        
        // Save it.
        self::saveImage($destPath, $imageQuality, $download);
    }
    
    /**
    * Resizes an image to a maximum height, maintaining the aspect ratio for the width, and creates the specified output image.
    * Optionally, the image can also be downloaded.
    * 
    * @param string $sourceImage  The path to the source image
    * @param string $destPath The path to the destination image
    * @param integer $height The maximum height to resize the image to
    * @param mixed $imageQuality The desired output quality (0-100)
    * @param boolean $download Set to true to trigger a download
    */
    public static function resizeToMaxHeightAndSave($sourceImage, $destPath, $height, $imageQuality = 100, $download = false)
    {
        // Load the source image
        self::getImage($sourceImage);
        
        // Resize it
        self::resizeTo(0, $height, MYNDIE_IMAGE_RESIZE_MAX_HEIGHT);
        
        // Save it.
        self::saveImage($destPath, $imageQuality, $download);
    } 
    
    /**
    * Resizes an image to a maximum width, maintaining the aspect ratio for the height, and creates the specified output image.
    * Optionally, the image can also be downloaded.
    * 
    * @param string $sourceImage  The path to the source image
    * @param string $destPath The path to the destination image
    * @param integer $height The maximum height to resize the image to
    * @param mixed $imageQuality The desired output quality (0-100)
    * @param boolean $download Set to true to trigger a download
    */
    public static function resizeToMaxWidthAndSave($sourceImage, $destPath, $width, $imageQuality = 100, $download = false)
    {
        // Load the source image
        self::getImage($sourceImage);
        
        // Resize it
        self::resizeTo($width, 0, MYNDIE_IMAGE_RESIZE_MAX_WIDTH);
        
        // Save it.
        self::saveImage($destPath, $imageQuality, $download);
    }       

    /**
    * Saves the new modified image back to disk
    *
    * $savePath - The path to save the image to.
    * $imageQuality - The quality of image to create
    *
    */
	public static function saveImage($savePath, $imageQuality = 100, $download = false)
	{
	    if(self::$newImage == false) {
            throw new Exception("Image::saveImage - New image is not yet created.  You probably need to perform an operation first.", 1);    
        }
        
        switch(self::$extension)
	    {
	        case 'image/jpg':
                imagejpeg(self::$newImage, $savePath, $imageQuality);
                break;
                
	        case 'image/jpeg':
                imagejpeg(self::$newImage, $savePath, $imageQuality);
                break;
                
	        case 'image/gif':
	        	// Check PHP supports this file type
                imagegif(self::$newImage, $savePath);
	            break;
                
	        case 'image/png':
	            $invertScaleQuality = 9 - round(($imageQuality / 100) * 9);
	            imagepng(self::$newImage, $savePath, $invertScaleQuality);
	            break;
                
            default:
                throw new Exception("Image::saveImage - Invalid image type.", 1);    
                break;
	    }
        
	    if($download) {
	    	header('Content-Description: File Transfer');
			header("Content-type: application/octet-stream");
			header("Content-disposition: attachment; filename= ". $savePath . "");
			readfile($savePath);
	    }
        
	    imagedestroy(self::$newImage);
	}
    
    /**
    * Resize the image to these set dimensions
    *
    * $width        	- Max width of the image
    * $height       	- Max height of the image
    * $resizeOption - Scale option for the image
    * 
    */
	public static function resizeTo( $width, $height, $resizeOption = '' )
	{
		switch(strtolower($resizeOption))
		{
			case MYNDIE_IMAGE_RESIZE_EXACT:
				self::$newWidth = $width;
				self::$newHeight = $height;
			break;
            
			case MYNDIE_IMAGE_RESIZE_MAX_WIDTH:
				self::$newWidth  = $width;
				self::$newHeight = self::resizeHeightByWidth($width);
			break;
            
			case MYNDIE_IMAGE_RESIZE_MAX_HEIGHT:
				self::$newWidth  = self::resizeWidthByHeight($height);
				self::$newHeight = $height;
			break;
            
			default:
				if(self::$originalWidth > $width || self::$originalHeight > $height) {
					if ( self::$originalWidth > self::$originalHeight ) {
				    	 self::$newHeight = self::resizeHeightByWidth($width);
			  			 self::$newWidth  = $width;
					} else if( self::$originalWidth < self::$originalHeight ) {
						self::$newWidth  = self::resizeWidthByHeight($height);
						self::$newHeight = $height;
					}
				} else {
		            self::$newWidth = $width;
		            self::$newHeight = $height;
		        }
			break;
		}
        
		self::$newImage = imagecreatetruecolor(self::$newWidth, self::$newHeight);
    	imagecopyresampled(self::$newImage, self::$image, 0, 0, 0, 0, self::$newWidth, self::$newHeight, self::$originalWidth, self::$originalHeight);
	}
	
    /**
    * Get the resized height from the width keeping the aspect ratio
    * $width - Max image width
    * Height keeping aspect ratio
    */
	private static function resizeHeightByWidth($width)
	{
		return floor((self::$originalHeight / self::$originalWidth) * $width);
	}
	
    /**
    * Get the resized width from the height keeping the aspect ratio
    * $height - Max image height
    * Width keeping aspect ratio
    */
	private static function resizeWidthByHeight($height)
	{
		return floor((self::$originalWidth / self::$originalHeight) * $height);
	}
	
	public static function cropImage($width, $height)
	{
		self::$newWidth = $width;
		self::$newHeight = $height;
        
		$leftcenter	= (self::$originalWidth / 2) - (self::$newWidth / 2);
		$topcenter 	=	(self::$originalHeight / 2) - (self::$newHeight / 2);
		self::$newImage = imagecreatetruecolor(self::$newWidth, self::$newHeight);
		imagecopy(self::$newImage, self::$image, 0, 0, $leftcenter, $topcenter, self::$originalWidth, self::$originalHeight);
	}
    
    /**
    * Loads an image into memory given the image path
    * and gets the image metadata
    * 
    * @param string $imagePath The path to the image to load
    */
    private static function getImage($imagePath)
    {
        self::$image = false;
        self::$newImage = false;
        self::$originalWidth = 0;
        self::$originalHeight = 0;
        self::$newWidth = 0;
        self::$newHeight = 0;
        
        if(!file_exists($imagePath)) {
            throw new Exception("Image file not at this path.", 1);
        }
        
        $size = getimagesize($imagePath);
        if(!is_array($size)) {
            throw new Exception("Unable to load image info.", 1);
        }
        
        self::$originalWidth = $size[0];
        self::$originalHeight = $size[1];
        
        self::$extension = $size['mime'];
        
        switch(self::$extension)
        {
            case 'image/jpg':
                self::$image = imagecreatefromjpeg($imagePath);
                break;
                            
            case 'image/jpeg':
                self::$image = imagecreatefromjpeg($imagePath);                
                break;
                
            case 'image/gif':
                self::$image = @imagecreatefromgif($imagename);
                break;
                
            case 'image/png':
                self::$image = @imagecreatefrompng($imagePath);
                break;
                
            // Mime type not found
            default:
                throw new Exception("File is not an image, please use another file type.", 1);
                break;
        }
    }    
}

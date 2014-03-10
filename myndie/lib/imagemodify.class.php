<?php
namespace Myndie\Lib;

class Image
{
	private $extension;
	private $image;
	private $newImage;
	private $originalWidth;
	private $originalHeight;
	private $newWidth;
	private $newHeight;

	public function __construct( $imagename )
	{
		if(file_exists($imagename))
		{
			
			$this->getImage( $imagename );
		} else {
			throw new Exception('Image ' . $imagename . ' can not be found, try another image.');
		}
	}
	
	/**
	 * Get the image variables
	 */
	 
	private function getImage( $imagename )
	{
		$size = getimagesize($imagename);
		$this->extension = $size['mime'];
		switch($this->extension)
	    {
	    	// Image is a JPG
	        case 'image/jpg':
	        case 'image/jpeg':
	        	// create a jpeg extension
	            $this->image = imagecreatefromjpeg($imagename);
	            
	            break;
	        // Image is a GIF
	        case 'image/gif':
	            $this->image = @imagecreatefromgif($imagename);
	            break;
	        // Image is a PNG
	        case 'image/png':
	            $this->image = @imagecreatefrompng($imagename);
	            break;
	        // Mime type not found
	        default:
	            throw new Exception("File is not an image, please use another file type.", 1);
	    }
	    $this->originalWidth = imagesx($this->image);
	    $this->originalHeight = imagesy($this->image);
	}
	
	/**
	 * Save the image as the original image type
	 *
	 * $savePath     - The path to  the new image
	 * $imageQuality 	  - The quality of image to create
	 *
	 */
	 
	public function saveImage($savePath, $imageQuality="100", $download = false)
	{
	    switch($this->extension)
	    {
	        case 'image/jpg':
	        case 'image/jpeg':
	        	// Check PHP supports this file type
	            if (imagetypes() & IMG_JPG) {
	                imagejpeg($this->newImage, $savePath, $imageQuality);
	            }
	            break;
	        case 'image/gif':
	        	// Check PHP supports this file type
	            if (imagetypes() & IMG_GIF) {
	                imagegif($this->newImage, $savePath);
	            }
	            break;
	        case 'image/png':
	            $invertScaleQuality = 9 - round(($imageQuality/100) * 9);
	            // Check PHP supports this file type
	            if (imagetypes() & IMG_PNG) {
	                imagepng($this->newImage, $savePath, $invertScaleQuality);
	            }
	            break;
	    }
	    if($download)
	    {
	    	header('Content-Description: File Transfer');
			header("Content-type: application/octet-stream");
			header("Content-disposition: attachment; filename= ".$savePath."");
			readfile($savePath);
	    }
	    imagedestroy($this->newImage);
	}
	/**
	 * Resize the image to these set dimensions
	 *
	 * $width        	- Max width of the image
	 * $height       	- Max height of the image
	 * $resizeOption - Scale option for the image
	 * 
	 */
	 
	public function resizeTo( $width, $height, $resizeOption = 'default' )
	{
		
		switch(strtolower($resizeOption))
		{
			case 'exact':
				$this->newWidth = $width;
				$this->newHeight = $height;
			break;
			case 'maxwidth':
				$this->newWidth  = $width;
				$this->newHeight = $this->resizeHeightByWidth($width);
			break;
			case 'maxheight':
				$this->newWidth  = $this->resizeWidthByHeight($height);
				$this->newHeight = $height;
			break;
			default:
				if($this->originalWidth > $width || $this->originalHeight > $height)
				{
					if ( $this->originalWidth > $this->originalHeight ) {
				    	 $this->newHeight = $this->resizeHeightByWidth($width);
			  			 $this->newWidth  = $width;
					} else if( $this->originalWidth < $this->originalHeight ) {
						$this->newWidth  = $this->resizeWidthByHeight($height);
						$this->newHeight = $height;
					}
				} else {
		            $this->newWidth = $width;
		            $this->newHeight = $height;
		        }
			break;
		}
		$this->newImage = imagecreatetruecolor($this->newWidth, $this->newHeight);
    	imagecopyresampled($this->newImage, $this->image, 0, 0, 0, 0, $this->newWidth, $this->newHeight, $this->originalWidth, $this->originalHeight);
    	
	}
	
	/**
	 * Get the resized height from the width keeping the aspect ratio
	 * $width - Max image width
	 * Height keeping aspect ratio
	 */
	 
	private function resizeHeightByWidth($width)
	{
		return floor(($this->originalHeight/$this->originalWidth)*$width);
	}
	
	/**
	 * Get the resized width from the height keeping the aspect ratio
	 * $height - Max image height
	 * Width keeping aspect ratio
	 */
	 
	 
	private function resizeWidthByHeight($height)
	{
		return floor(($this->originalWidth/$this->originalHeight)*$height);
	}
	
	public function cropimage($width, $height)
	{
		$this->newWidth = $width;
		$this->newHeight = $height;
		$leftcenter	=	($this->originalWidth / 2) - ($this->newWidth / 2);
		$topcenter 	=	($this->originalHeight / 2) - ($this->newHeight / 2);
		$this->newImage = imagecreatetruecolor($this->newWidth, $this->newHeight);
		imagecopy($this->newImage, $this->image, 0, 0, $leftcenter, $topcenter, $this->originalWidth, $this->originalHeight);
		
	}
}

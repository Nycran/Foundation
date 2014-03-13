<?php
namespace Myndie\Model;  

use RedBean_Facade as R; 
use Myndie\Lib\Strings;

class Image extends Model
{
    private $allowedImageFor;
    
    public function __construct($app)
    {
        $this->app = $app;
        $this->table = "image";
        
        // Call parent constructor
        parent::__construct($app);
        
        $this->defaultOrderBy = "id ASC";
        
        $this->allowedImageFor = array("sponsor_logo");        
    }
    
    protected function applyFilters($filters, &$where = "", &$values = array()) 
    {
        if(array_key_exists("image_for", $filters)) {
            if(!empty($where)) {
                $where .= " AND ";
            }
            
            $where .= "image_for = ? "; 
            $values[] = $filters["image_for"];  
        }
        
        if(array_key_exists("foreign_id", $filters)) {
            if(!empty($where)) {
                $where .= " AND ";
            }
                        
            $where .= "foreign_id = ? "; 
            $values[] = $filters["foreign_id"];  
        }        
    }
    
    /**
    * prepareUploadFolder ensures that the Myndie uploads folder is writable and
    * creates a directory (if it doesn't already exist) to store the imageFor type.
    * 
    * @param string $imageFor Defines what the image will be used for.
    * @param string $id The ID of the thing the image is being uploaded for
    * @param string $error An output param that will hold an error message if an error occurs.
    */
    public function prepareUploadFolder($imageFor, $id, &$error = "")
    {
        // Ensure the imageFor is allowed
        if((empty($imageFor)) || (!in_array($imageFor, $this->allowedImageFor))) {
            $error = "Invalid Image For Type: " . $imageFor;
            return false;
        }
        
        // Ensure the upload folder exists and is writable
        $uploadsPath = MYNDIE_ABSOLUTE_PATH . "uploads/";
        if((!is_dir($uploadsPath)) || (!is_writable($uploadsPath))) {
            $error = "Uploads folder does not exist on server or is not writable";    
            return false;
        }  
        
        // Make a folder specifically for the "imageFor"
        $destFolder = $uploadsPath . $imageFor . "/";
        if(!is_dir($destFolder)) {
            @mkdir($destFolder);
            @chmod($destFolder, 0777);
            
            if((!is_dir($destFolder)) || (!is_writable($destFolder))) {
                $error = "imageFor folder $destFolder could not be created or is not writable";        
                return false;
            }
        }
        
        // Make a folder specifically for this ID within the imageFor
        $destFolder = $destFolder . $id . "/";
        if(!is_dir($destFolder)) {
            @mkdir($destFolder);
            @chmod($destFolder, 0777);
            
            if((!is_dir($destFolder)) || (!is_writable($destFolder))) {
                $error = "imageFor folder $destFolder could not be created or is not writable";        
                return false;
            }
        }        
        
        return $destFolder;
    }
    
    /**
    * Handles an XHR image upload and saves the file to the specified folder
    * 
    * @param string $destFolder The folder to save the file to
    * @param string $fileName The name of the file to save
    * @param string $error Output param - will hold an error message on failure
    * @return The path to the saved file on success, false on failure.
    */
    public function handleImageUpload($destFolder, $fileName, &$error = "")               
    {
        // Ensure the destination folder exists.
        if((!is_dir($destFolder)) || (!is_writable($destFolder))) {
            $error = "imageFor folder $destFolder could not be created or is not writable";        
            return false;
        } 
        
        // Define the final path of the saved file
        $path = $destFolder . $fileName;       
        
        // Attempt to save the file
        $result = $this->handleXHRUpload($path);   
        
        if(!$result) {
            $error = "Image upload for $fileName failed";        
            return false;            
        }
        
        return $path;         
    }
    
    public function saveToDB($imageFor, $foreign_id, $path)
    {
        if(!file_exists($path)) {
            return false;    
        }
        
        $size = filesize($path);
        
        $imageData = getimagesize($path);
        if(!is_array($imageData)) {
            return false;
        }
        
        $width = $imageData[0];
        $height = $imageData[1];
        $type = $imageData[2];
        
        // Remove the absolute path from the uploaded path
        $path = str_replace(MYNDIE_ABSOLUTE_PATH, "/", $path);
        
        // Store the image meta data in the database
        $data = array();
        $data["image_for"] = $imageFor;
        $data["foreign_id"] = $foreign_id;
        $data["path"] = $path;
        $data["size"] = $size;
        $data["width"] = $width;
        $data["height"] = $height;
        $data["type"] = $type;
        
        $id = $this->save("", $data);
        if(!$id) {
            return false;
        }
        
        return $id;
    }
    
    /**
    * Deletes an image from the filesystem and database
    * 
    * @param object $imageBean A RedBean image bean
    * @param string $error An output param - will hold an error message if a problem occurs.
    * @return True on success, false on failure.
    */
    public function deleteImage($imageBean, &$error = "")
    {
        if(!$imageBean) {
            return false;
        }
        
        $path = MYNDIE_ABSOLUTE_PATH . $imageBean->path;
        if(file_exists($path)) {
            @unlink($path);
            
            if(file_exists($path)) {
                $error = "Image could not be deleted from filesystem";
                return;    
            }
        }
        
        if(!$this->delete($imageBean->id)) {
            $error = "Image bean could not be deleted";
            return false;
        }
        
        return true;
    }
    
    /**
    * Handles an XHR file upload and saves the content to the specified path
    * 
    * @param string $path The path to upload the file to.
    * @return The path on success, false on failure.
    */
    private function handleXHRUpload($path)
    {  
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);

        if ($realSize != $this->getSize()){            
            return false;
        }

        $target = fopen($path, "w");        
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);

        return true;     
    }
    
    /**
    * Gets the size of the file upload from the web server headers
    * @returns The size of the upload in bytes.
    */
    private function getSize()
    {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];            
        } else {
            throw new Exception('Getting content length is not supported.');
        }      
    }    
}

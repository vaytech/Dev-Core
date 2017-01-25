<?php

/**
 *
 * this is a file uploads class
 *
 * handles file uploads and is an extension of the the OR Database (OR_Dbase) class
 *
 * @package OR_uploads
 * @author Vincent Agyei - Yeboah <psalm92v12@gmail.com>
 * 
 */

/**
 *
 * IMPORTANT
 * 	when including this class, always make sure that the database class has been required in the initialization file and that the database class has been required first
 */


class OR_uploads{
        
    /**
     * @param string $type the filetypes that will be accepted;
     */
    private $type = NULL;  
      
      
    /**
     * @param string $fieldname the name of the form file input field
     */
    private $fieldname = "";
    
    
    /**
     * @param string $uploaddir the name of the directory where the file will be sent 
     */
    private $uploaddir = NULL;
    
    /**
     * @param string $tmpfile the temporary file name 
     */
    private $tmpfile;
    
    /**
     * the name we want to give to the file before saving it to its destination
     */
    private $targetname;
    
    /**
     * @param integer $filesize the size of the file
     */
    private $filesize;
    
    /**
     * target is the name of the full path where the file will be stored plus the file name itself
     */
    private $target;
    
    
    /**
     * constructor
     *
     * accepts some arguments
     *
     * @param array $type accepted file types that the upload will allow (e.g "image/jpeg", "image/png")
     * @param string $fieldname the name of the form file field
     * @param string $uploaddir the directory where the file will be permanently stored
     */
    
    public function __construct($type = NULL, $fieldname = "", $uploaddir = NULL){
	/* set the accepted file types to the ones provided in the argument */
        $this->type = $type;
        
        /* set the form file input field to the one provided in the argument */
	$this->fieldname = $fieldname;
        
        /* set the permanent storage directory to the one provided in the argument */
	$this->uploaddir = $uploaddir;
        
        /* retrieve the temporary file name from the submitted form and set it to the tempfile variable */
	$this->tmpfile = $_FILES[$this->fieldname]['tmp_name'];
        
        /* set the targetname*/
	$this->targetname = basename($_FILES[$this->fieldname]['name']);
        
        /* set the target */
	$this->target = $this->uploaddir."/".$this->targetname;
        
        /* get the file size */
	$this->filesize = $_FILES[$this->fieldname]['size'];
    }
    
    /**
     * retrieves the file info
     *
     * every file upload comes with some information like the size, errors, etc.
     * this function prints those information in an array
     *
     * @return void does not return anything
     */
    private function uploadarray(){
        
        foreach ($_FILES as $key=> $values){
            foreach ($values as $key=> $value){
                echo $key.": ".$value."<br />";
            }
        }
        echo "<hr />";
    }
    
    /**
     * checks the upload for errors before attempting to move it to a permanent storage
     * 
     * @param string $ok this variable holds a TRUE or FALSE value and will be returned after all is done
     * @param string $goodfile this variable will be used to test whether the file is an accepted file type or not
     * 
     * @return boolean returns a boolean TRUE if everything is ok and FALSE if everything is not ok
     */
    
    public function checkupload(){
	
	/**
         * check if there are any file upload errors
         */
	if($_FILES[$this->fieldname]['error'] > 0){
	    $ok = false;
	    
	/**
	 * if there are no errors then
	 * initiate the value of $errors to FALSE and that of $goodfile to FALSE also
	*/
	}elseif ($_FILES[$this->fieldname]['error'] == 0){
	    $errors = false;
	    $goodfile = false;
	    
	    /**
	     * use this method if the accepted file types value passed in as an argument is an array
	     * loop through the array and check if the file type returned from the upload matches with
	     * any of the accepted file types indicated by the user
	     * 
	     */
            if (is_array($this->type)){
                foreach ($this->type as $required){
                   if($required == $_FILES[$this->fieldname]['type']){
                        $goodfile = true;
                    }else{
                        NULL;    
                    }
                }
		
	    /**
	     * use this method if the accepted file types value passed in as an argument is not an array
	     */
            }elseif (!is_array($this->type)){
                    if($_FILES[$this->fieldname]['type'] == $this->type){
                        echo $required." ".$_FILES[$this->fieldname]['type']."<br />";
			$goodfile = true;
                   }else{
                        NULL;
                   }
            }
	    
	    /**
	     * check if the file already exists
	     */
	    if ($goodfile == false){
	        $ok = false;
	    }elseif ($goodfile == true){
	        if (file_exists($this->target)){
	            $fileexists =  true;
		    $ok = false;
	        }else{
		    $fileexists = false;
		    $ok = true;
		}
	    }
	}
        return $ok;
    }
    
    /**
     * move the uploaded file from temporary directory to permanent directory
     * @param string $message the message that is produced
     * @return string returns a message stating whether the transfer was successful or not
     */
    public function uploadfile($rename = NULL){
	/**
	 * if you check the upload and everything is fine then transfer the file from temp dir to permanent dir
	 * then check if the file was successfully moved from temp to permanent directory
	 * if it is then file was uploaded successfully else file was not uploaded
	*/
	if ($this->checkupload()){
                if ($rename != NULL){
		    $this->target = $this->uploaddir."/".$rename;
			if(move_uploaded_file($this->tmpfile, $this->target)){
			    $message = TRUE;
			}else{
			    $message = FALSE;   
			}	
		}else{
			if(move_uploaded_file($this->tmpfile, $this->target)){
			    $message = TRUE;
			}else{
			    $message = FALSE;   
			}	
		}
		       
        }else{
	    $message = FALSE;
	}
    return $message;
    }
       
    
}

?>
<?php

/* these are used to define the url paths */
if(DEV_STAGE == "dev"){
	defined("FIRST") ? NULL : define("FIRST", 2);
	defined("SECOND") ? NULL : define("SECOND", 3);
	defined("THIRD") ? NULL : define("THIRD", 4);
	defined("FOURTH") ? NULL : define("FOURTH", 5);
	defined("FIFTH") ? NULL : define("FIFTH", 6);
}
elseif(DEV_STAGE == "live"){
	defined("FIRST") ? NULL : define("FIRST", 1);
	defined("SECOND") ? NULL : define("SECOND", 2);
	defined("THIRD") ? NULL : define("THIRD", 3);
	defined("FOURTH") ? NULL : define("FOURTH", 4);
	defined("FIFTH") ? NULL : define("FIFTH", 5);
}

class ROUTER{


/**
 * [getUrl description]
 * @return [type] [description]
 */
static private function getUrl($key = null){
	
	$path = $_SERVER['REQUEST_URI'];
	$break_path = explode("/", $path);

	if(isset($key) && !empty($key)){
		if(isset($break_path[$key])){
			return $break_path[$key];
		}
		else{
			return null;
		}
	}
	else{
		return $break_path;
	}

	
}

/**
 * [get description]
 * @return [type] [description]
 */
static public function get($key = null){

	if(isset($key) && !empty($key)){
		return self::getUrl($key);
	}
	else{
		return self::getUrl();
	}

}

/**
 * [post description]
 * @return [type] [description]
 */
static public function post($key = null){
	
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		if(isset($key) && !empty($key)){
			if(isset($_POST[$key])){
				return $_POST[$key];
			}
			else{
				return null;
			}
		}
		else{
			return $_POST;
		}
	}

}

}


?>
<?php  
if(!isset($_SESSION)){
	session_start();
}

class OR_authentication{

static private function getModel(){
	return new OR_AUTH_Model;
}


/**
* check if the user is logged in based on session
*/
static public function isLoggedIn(){
	if(isset($_SESSION['loggedin'])){
		return true;
	}
	else{
		return false;
	}
}

/**
 * [startUserSession description]
 * @param  [type] $email [description]
 * @return [type]        [description]
 */
static public function startUserSession($email){
	$_SESSION['loggedin'] = $email;
	return true;
}

/**
 * [logoutUser description]
 * @return [type] [description]
 */
static public function logoutUser(){
	unset($_SESSION);
	$_SESSION = array();
	session_destroy();
	//header("Location: ".URL_PATH);
}


/**
 * [getLoggedinUser description]
 * @return [type] [description]
 */
static public function getLoggedinUser(){
	return isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : NULL;
}


/**
 * [resetPassword description]
 * @param  [type] $username [description]
 * @return [type]           [description]
 */
static public function resetPassword($username){

}



}

?>
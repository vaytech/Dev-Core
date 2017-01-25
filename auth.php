<?php  
if(!isset($_SESSION)){
	session_start();
}

class OR_auth{


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



}

?>
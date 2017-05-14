<?php  

require_once "or_pdo_class.php";

class OR_AUTH_Model extends OR_Dbase{


/**
 * [addUser description]
 * @param [type] $email    [description]
 * @param [type] $password [description]
 */
public function addUser($tablename, $fields, $values){

	if($this->insertrecord($tablename, $fields, $values)){
		return true;
	}
	else{
		return false;
	}

}


/**
 * check that user exists
 * @param  [type] $email [description]
 * @return [type]        [description]
 */
public function checkUserExists($field, $value, $table){
	return $this->pdo->query("select * from $table where $field = '".$value."' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
}


/**
 * [loginUser description]
 * @param  [type] $email    [description]
 * @param  [type] $password [description]
 * @return [type]           [description]
 */
public function loginUser($usernamefield, $passwordfield, $email,$password, $table){
	return $query = $this->pdo->query("select id from $table where $usernamefield = '".$email."' and $passwordfield = '".$password."' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
}



}


?>
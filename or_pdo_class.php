
<?php


/**
 * database class
 *
 * a database class for handling mysql databases
 * it handles tasks like creating tables, reading and updating etc.
 *
 * @package Or_Dbase
 * @author Vincent Agyei - Yeboah <psalms92v12@gmail.com>
 * 
 */


class OR_Dbase
{
   
    /**
     * define database variables
     *
     * @param string $db_host the database host
     * @param string $db_user the username for the database
     * @param string $db_pass the password for the database
     * @param strng $db_name the database name
     */
   
    private $db_host;
    private $db_user;
    private $db_pass;
    private $db_name;
    
    private $pdo;

    public $last_query;
    private $magic_quotes_active;
    private $real_escape_string_exists;
	
    private $con = false;

    /**
     * constructor
     *
     * connects to the database
     * turns magic quotes on
     * turns on escapes for strings
     *
     */
    
public function __construct($db_host = "localhost", $db_user = "admin", $db_pass = "password", $db_name="comn8") {
$this->db_host = $db_host;
$this->db_user = $db_user;
$this->db_pass = $db_pass;
$this->db_name = $db_name;

$this->pdoconnect();
$this->magic_quotes_active = get_magic_quotes_gpc();
$this->real_escape_string_exists = function_exists( "mysql_real_escape_string" );
}
        
//connect to the database
protected function pdoconnect()    {
    $dsn = "mysql:host=".$this->db_host.";dbname=".$this->db_name.";charset=utf8";
    
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $this->pdo = new PDO($dsn, $this->db_user, $this->db_pass, $opt);

}
    
    /**
     * helps to insert records into a database table
     *
     *	@param string $table the name of the table
     *	@param array $fields an array of column names as they appear in the database table and in the order they appear
     *	@param array $values an array of values that was returned from the form to be inserted into the database
     *	
     * @return boolean returns TRUE if record insert was successful but FALSE if it was not successful
     */
    
    public function insertrecord($table ="", $fields="", $values=""){
        
        $pdvalues = array();

        $query = "INSERT INTO $table (";
    
        foreach($fields as $key => $value){
            if ($key == (count($fields) - 1)){
                $query.=$value;
            }else{
                $query.=$value.", ";    
            }
        
        }
    
        $query.= ") VALUES (";
    
        foreach($values as $key => $value){
            if ($key == (count($fields) - 1)){
                $value = OR_tools::paragraphs(htmlentities($value, ENT_QUOTES)); // maintains paragraphs and prevents dangerous inputs
                $query.="?";
                $pdvalues[] = $value;
            }else{
                $value = OR_tools::paragraphs(htmlentities($value, ENT_QUOTES)); // maintains paragraphs and prevents dangerous inputs
                $query.="?".", ";    
                $pdvalues[] = $value;
            }
        
        }
    
        $query.=")";


        //echo $query."<br /><br />"; //display query for testing purposes only

        try{
            $statement = $this->pdo->prepare($query);
            $statement->execute($pdvalues);    
        }
        catch (PDOException $e){
            throw $e;
        }
        
    
        if ($statement->lastInsertId() > 0){
            return TRUE;
        }else{
            return FALSE;
        }
    
}

#update values in database function

    public function updaterecord($table="", $fields ="", $id, $idtype){
	/**
	 * always encode values before you insert into the database
	 */
	
	foreach ($fields as $keys => $values){
	    OR_tools::encodevalue($values, "html");
	    //echo $values."<br />";
	}
	
	
        $query ="UPDATE $table SET " ;
	//echo count($fields);
	$count = 1;
	foreach ($fields as $key => $inputs){
	    if ($count == (count($fields))){
                $query.= "{$key} = '".OR_tools::paragraphs(htmlentities($inputs, ENT_QUOTES))."'";
            }else{
                $query.= "{$key} = '".OR_tools::paragraphs(htmlentities($inputs, ENT_QUOTES))."', ";
            }
	    $count++;
	}
	$query.=" WHERE ".$idtype." = '{$id}' LIMIT 1" ;
	
	//echo $query;
	
	$result = mysql_query($query);
    
        if (mysql_affected_rows() >= 0){
            return true;
        }else{
            return false;
        }
    
    }


#delete values in database function

    public function deleterecord($table="", $id="", $idtype){
        $this->dbconnect();
        $query = "DELETE FROM $table WHERE ".$idtype." = '{$id}' LIMIT 1";
        //echo $query;
        $result = mysql_query($query);
        
        if (mysql_affected_rows() > 0){
            return TRUE;
        }else{
            return FALSE;
        }
        
    }

#select all records

    public function selectallrecords($table = NULL){
        $query = "SELECT * FROM $table";
	//echo $query;
        $result = mysql_query($query);
		
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
	    $values[] = $row;
	}
	
	//if(isset($values) && count($values) > 0){
	//    foreach ($values as $c) {
	//        while (list($k, $v) = each ($c)) {
	//           echo "$k ... $v <br/>";
	//        }
	//    }    
	//}
	
	
	//print_r($return);
	
        return (isset($values)) ? $values : NULL;
    
        
    }
    
    //affected rows
    public function affected_rows(){
        return mysql_affected_rows($this->con);
    }

    /**
     * looks up the database to see if a record exists
     *
     * @return boolean returns true if the record exists and false if it does not exist
     */
    
	public function findbyfield($table = NULL, $field= NULL, $value = NULL){
	$this->dbconnect();
        $query = "SELECT * FROM $table WHERE $field = '{$value}'";
        $result = mysql_query($query);
	
        if ($this->affected_rows() == 1){
            return true;
        }
        else{
            return false;
        }
        
    }

#select one record by id
	public function findbyid($table = NULL, $idtype = NULL, $id = NULL){
	$this->dbconnect();
        $query = "SELECT * FROM $table WHERE ".$idtype." = '{$id}'";
        //echo $query;
        $result = mysql_query($query);
	
	if ($result){
	    $rows = $this->fetcharray($result);
	    return $rows;    
	}else{
	    return NULL;
	}
        
    }

/**
 * checks if a record exists or not in a database
 *
 * @return boolean returns a boolean true if the record is found and a boolean false if the record is not found
 */

    public function checkrecordsexists($table="",$field="",$value=""){
        
	/* connect to the database */
	$this->dbconnect();
	
	/*create the query statement */
        $query = "SELECT * FROM $table WHERE $field = '{$value}' LIMIT 1"; //echo $query;
        
	/*query the database */
        $result = mysql_query($query) or die(mysql_error());
		
	/* if the number of rows returned is equal to 1 then it means the record exists so return TRUE else return FALSE */
        if (mysql_num_rows($result) == 1){
            return true;
        }else{
            return false;
        }
    }

/**
 * checks if a user exists
 *
 * checks the username and password supplied by a user and check if it matches with that recorded in the database
 * this primarily is used for user authentication
 *
 * @param $username this is the name the user uses to login
 * @param $password this is the password the user has to supply to get in
 * @param $table this is the name of the table that stores all the usernames and passwords
 * @param $usernameField this is the name of the username field,
 * this might be different for every authors based on the way they create their databases
 * @param $passwordField this is the name of the password field,
 * again this might be different for every author based on the way they create their databases 
 *
 * @return boolean will return a boolean true if the user is found or a boolean false if the user is not found
 */

    public function checkuserexists($username = NULL, $password = NULL, $table = NULL, $usernameField = NULL, $passwordField = NULL){
        /* connect to the database */
	$this->dbconnect();
	
	/*escape the username to prevent dangerous inputs */
        $username = $this->escape_value($username);
	
	/*encrypt the password with SHA_1 encryption*/
        $password = OR_tools::encryptvalue($this->escape_value($password));
	
	/*create the query*/
        $query = "SELECT $usernameField FROM $table WHERE $usernameField = '$username' AND $passwordField = '$password' LIMIT 1";
        //echo $query;
	/*now query the database*/
	
	/*if the result returns 1 row then a user was found and returns a boolean true else returns a boolean false*/
        if (mysql_num_rows(mysql_query($query)) == 1){
            return true;
        }else{
            return false;
        }
	
    }

#mysql fetch array
    public function fetcharray($result = NULL){
        return mysql_fetch_array($result, MYSQL_ASSOC);
    }

#mysql query
    public function query($query){
        mysql_query($query);
    }
    

#count all method

   public function countall($table = NULL){
        $this->dbconnect();
        
        $query = "SELECT COUNT( * ) FROM ".$table;
	//echo $query."<br />";
        $result = mysql_query($query);
        $row = mysql_fetch_array($result);
        $count = array_shift($row);
	
	//print_r($count);
	
	if ($count > 0){ //if there are records in the database then return the number of items
	    return $count;
	}else{ //return 0;no records in the database
	    return 0;
	}
	
    
    }
    
    /**
     * performs a MySQL query and returns a result set or a false
     *
     * @return boolean returns FALSE if the query returned no records and a result set if the query was successful
     */
    
    public function findbysql($query="") {
	//echo $query;
        $this->dbconnect();
        $result = mysql_query($query);
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $object_array[] = $row;
        }
	
	if (isset($object_array) && count($object_array) > 0){
	    return $object_array;    
	}else{
	    return FALSE;
	}
        
    }
    
    //escape values    
    public function escape_value($value) {
                $value = trim($value);
		if( $this->real_escape_string_exists ) { // PHP v4.3.0 or higher
			// undo any magic quote effects so mysql_real_escape_string can do the work
			if( $this->magic_quotes_active ) { $value = stripslashes( $value ); }
			$value = mysql_real_escape_string( $value );
		} else { // before PHP v4.3.0
			// if magic quotes aren't already on then add slashes manually
			if( !$this->magic_quotes_active ) { $value = addslashes( $value ); }
			// if magic quotes are active, then the slashes already exist
		}
		return $value;
    }
    
    
}


/**
 *
 * pagination class to paginate objects
 *
 * example:
 * 
 * create a new pagination object with limit, current page and total number of items arguments
 * $pagination = new pagination($limit,tools::currentgetpage(),$this->database->countall("products"));
 *  	
 * then create the sql query to select all the records from the table providing limits and offset options
 * $query = "SELECT * FROM products LIMIT $limit OFFSET ".$pagination->offset()."";
*/

class OR_pagination extends Or_Dbase{
    
public $limit; /* the number of records per page */
public $currentpage; /* the current page we are on */
public $totalcount; /* the total number of records to be paginated */
    
    
public function __construct($limit = 1, $currentpage = 1, $totalcount = 0){

//always make sure that the value received is a positive
$currentpage = abs((int)$currentpage);

$this->limit = (int)$limit;
$this->totalcount = (int)$totalcount;

if ((int)$currentpage > $this->totalpages()){
	$this->currentpage = (int)$this->totalpages();
}elseif (gettype($currentpage) != "integer"){
	$this->currentpage = 1;    
}else{
	$this->currentpage = (int)$currentpage;    
}


}

#find total number of pages needed for the pagination
public function totalpages(){
	return ceil($this->totalcount/$this->limit);
}

#offset value
public function offset(){
	return abs(($this->currentpage - 1) * $this->limit);
}

#previous page
public function prevpage(){
	return $this->currentpage - 1;
}

#next page
public function nextpage(){
	return $this->currentpage + 1;
}    

#has next page
public function hasprevpage(){
	return $this->prevpage() >= 1 ? true : false;
}

#has previous page
public function hasnextpage(){
	return $this->nextpage() <= $this->totalpages() ? true : false;
}

/**
 * static pagination functions
 */

/* returns the current page that is being viewed */
public function currentPage($pageVar = ""){
    $page = (!empty($_GET[$pageVar])) ? abs((int)$_GET[$pageVar]) : 1;
    return $page;
}

/* this is used to return to the pagination list after viewing a record in detail */
public function backtolist($listtype, $pageVar){
	if (isset($_GET['cat'])){
		$message = "<a class='hr' href='".$_SERVER['PHP_SELF']."?act={$listtype}&cat=".$_GET['cat']."&id=".tools::getid()."&".$pageVar."=".tools::currentgetpage()."'>&laquo; Back to list view</a>";
	}else{
		$message = "<a class='hr' href='".$_SERVER['PHP_SELF']."?act={$listtype}&".$pageVar."=".tools::currentgetpage()."'>&laquo; Back to list view</a>";    
	}
	return $message;
}
    
    
/* this is a function to return an edit link */
public function editlink($values, $id, $type, $pageVar){
	$edit = "<a href='".$_SERVER['PHP_SELF']."?act=edit".$type."&id=".$id."&".$pageVar."=".tools::currentgetpage()."'>Edit</a>";
	return $edit;
}

/* this is a function to return a delete link */
public function deletelink($itemId, $itemName, $pageVar){
	$delete = "<a class='deleteLink' title='Delete' href='".$_SERVER['PHP_SELF']."?act=delete".$itemName."&id=".$itemId."&".$pageVar."=".$this->currentPage($pageVar)."'>Delete</a>";
	return $delete;
}

/* this is a function to return a details link */
public function detailslink($values, $id, $type, $pageVar){
    $details = "<a href='".$_SERVER['PHP_SELF']."?act=details".$type."&id=".$id."&".$pageVar."=".tools::currentgetpage()."'>Details</a>";
    return $details;
}

/* this is a function to return a previous link */
public function prevlink($listtype, $pageVar){
if ($this->hasprevpage()){
	$prev = "<a class='prevpage' href='".$_SERVER['PHP_SELF']."?act={$listtype}&".$pageVar."=".$this->prevpage()."'>Previous</a>";    		
}
else{
	$prev = NULL;
}
return $prev;
}

/* this is a function to return a next link */
public function nextlink($listtype, $pageVar){
if($this->hasnextpage()){
$next = "<a class='nextpage' href='".$_SERVER['PHP_SELF']."?act={$listtype}&".$pageVar."=".$this->nextpage()."'>Next</a>";    	
}
else{
$next = NULL;
}
return $next;
}
    
/* this is a function to display the pagination numbers */
public function pagenumbers($listtype, $pageVar){
$numbers = NULL;
$numbers.= "<div class='pageNumbers'>";
//$numbers.= "<p>showing Page ". $this->currentpage ." of ".$this->totalpages()."</p>";
for ($i=1; $i<=$this->totalpages(); $i++){
	if ($i == $this->currentpage){
		if (isset($_GET['cat']) && isset($_GET['id'])){
			$numbers.= "<a class='activePageNumber'href='".$_SERVER['PHP_SELF']."?act={$listtype}&".$pageVar."={$i}&cat=".$_GET['cat']."&id=".$_GET['id']."'> {$i} </a>";
		}else{
			$numbers.= "<a class='activePageNumber'href='".$_SERVER['PHP_SELF']."?act={$listtype}&".$pageVar."={$i}'> {$i} </a>";
		}
	}
	
	else{
			if (isset($_GET['cat']) && isset($_GET['id'])){
				$numbers.= "<a href='".$_SERVER['PHP_SELF']."?act={$listtype}&".$pageVar."={$i}&cat=".$_GET['cat']."&id=".$_GET['id']."'> {$i} </a>";    
			}else{
				$numbers.= "<a href='".$_SERVER['PHP_SELF']."?act={$listtype}&".$pageVar."={$i}'> {$i} </a>";        
		}
	}
}
$numbers.= "</div>";

return $numbers;
}




/* end of the class */
}

?>
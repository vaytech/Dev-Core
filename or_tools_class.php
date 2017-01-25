<?php

/**
* Utilities to add more functionality to a website
*
* a set of php codes that will be used to add more functionality to a website
*
* @package OR Systems
* @version 1.0
* @copyright Copyright (c) 2011, OR Systems
* @author Vincent Agyei - Yeboah <psalms92v12@gmail.com>
* 
*/


class OR_tools{

/**
* Check form for empty values
*
* Checks a form for empty values and returns an error message that lets the users know if there are any fields left to fill
* This function requires that all fields be field and should be modified if you want to make some fields optional
*
* @param array $_POST[$form] the name of the submit form
* @param string $message the error message generated from the process
* 
* @return string returns the error message or a NULL
* 
*/


static public function checkemptyvalues($form){
   
   if (isset($_POST[$form])){

   /**
    * @param integer $total the total number of post variables minus the submit button
    * @param integer $count the counter used during the looop
    */
   
   $total = (count($_POST)-1); 
   $count = 0; 
   
   /**
    * first of all loop through the submitted post variables
    * if the counter ($count) is equal to the total number of post variables then stop
    * if the the value of the post variable ($value) is empty then add it to the error array ($errors[])
    * continue to the next level of there is no empty value
    * then increment the counter
    */
   
   foreach ($_POST as $key => $value){ 
       if ($count == $total){ 
           continue;  
       }else{
           if ($value == "" || $value == NULL){ 
                   $errors[] = $key;
               }else {
                   continue; 
           }
       }
       $count++; 
       
   }
   
   /**
    * if the error array is set and its count is greater than 0
    * then report there are errors and generate the error message
    * else return NULL meaning there are no errors
    */
           
   if (isset($errors) && count($errors > 0)){ 
   (count($errors) == 1) ? $message = "<p class='warning'>You have 1 field to fill</p>" : $message = "<p class='warning'>You have some fields to fill</p>";
       return $message;    
   }else{
       return NULL; 
   }
   
   }
}


/**
* Return an error or success notice
*
* Returns an error or success notice to help a user know if an action or process was successful or not.
* The notice is wrapped in a <p> tag and has a warning or success class that can be used to style the notice in a stylesheet
*
* @param string $message the message returned from a process
* @param string $type the type of message (error or success)
* @param string $notice the notice that will be returned to the user
*/

static public function notice($message, $type){
   //echo "<br />"."notice:".$message." type:".$type."<br />";
   if (isset($message) && $message != "" && $type == "warning" || isset($message) && $message != NULL && $type == "warning"){
       $notice = "<p class='warning'>{$message}</p>";    
   }if (isset($message) && $message != "" && $type == "success" || isset($message) && $message != NULL && $type == "success"){
       $notice = "<p class='success'>{$message}</p>";    
   }elseif(!isset($message) && !isset($type) || $message == "" && $type == "" || $message == NULL && $type == NULL){
       $notice = "";
   }
   return $notice;
}

#sticky update values
static public function stickyupdatevalues($value,$updatevalue){
   // sticky values will store the values from a databse or in an update scenario
   // or it wil retain the values from a submitted from
   if (isset($_POST[$value])){
       return $_POST[$value];
   }elseif (!isset($_POST[$value]) && $updatevalue != NULL){
       return $updatevalue;
   }else {
       NULL;
   }
}

#sticky values
static public function stickyvalues($value = ""){
   // sticky values will store the values from a databse or in an update scenario
   // or it wil retain the values from a submitted from
   if (isset($_POST[$value])){
       return $_POST[$value];
   }elseif (!isset($_POST[$value])){
       NULL;
   }
}

#maintain paragraphs and line breaks from textboxes
static public function paragraphs($text = NULL){
   // Convert Windows (\r\n) to Unix (\n)
       $text = str_replace("\r\n", "\n", $text);
       // Convert Macintosh (\r) to Unix (\n)
       $text = str_replace("\r", "\n", $text);
       // Handle paragraphs
       $text = str_replace("\n\n", '<p></p>', $text);
       // Handle line breaks
       $text = str_replace("\n", '<br />', $text);
       
       return $text;
}

static public function convert_paragraphs_for_textbox($text = NULL){
// Convert Windows (\r\n) to Unix (\n)
$text = str_replace("\n", "\r\n", $text);

// Convert Macintosh (\r) to Unix (\n)
$text = str_replace("\n", "\r", $text);

// Handle paragraphs
$text = str_replace('<p></p>', "\n\n", $text);

// Handle line breaks
$text = str_replace('<br />', "\n", $text);
   
return $text;
}

/**
* converts paragraphs and line breaks into correct format to use in HTML pages
*/

static public function convert_to_html_paragraphs($string = NULL){
// Convert Windows (\r\n) to Unix (\n)
$text = str_replace("\n", "\r\n", $string);

// Convert Macintosh (\r) to Unix (\n)
$text = str_replace("\n", "\r", $string);

// Handle paragraphs
$text = str_replace("<p></p>", "<br /><br />", $string);

// Handle line breaks
//$text = str_replace("<br />", "<br />", $string);
   
return $text;
}

static public function login($loginpage = NULL){
   //if the user is not logged in (the loggin session variable is not set) then redirect them to the login page
   if (!isset($_SESSION["loggedin"])){
       self::redirectto($loginpage);
   }
   //if the user initiates a logout then:
   elseif (isset($_GET["act"]) && strtoupper($_GET["act"]) == "LOGOUT"){
       //destroy the session
       unset($_SESSION["loggedin"]);
       //and redirect to the login page
       self::redirectto($loginpage);
   }
   //else do nothing
   else{
       NULL;
   }
}

/**
 * this is used to return a part of a string
 * uses the substr function but modified to make sure
 * that it only returns a part of a string if the length
 * of the string is greater than the length provided
 * in the argument
 * @param integer $length length of the string to return
 * @param string $string the string to be chopped
 * @return string the chopped string
 */
static public function chopString($length, $string){
if (strlen($string) < $length){
   // this will add characters after the string if the length of the string
   //is less than what is provided in the argument
   //return str_pad($string, $length, ".");
   return $string;
}
elseif(strlen($string) == $length){
   return $string;
}
else{
   return substr($string, 0, ($length - strlen($string)));  
}
}
    
/**
* redirects the user to a specified page
*
* @param string $page the name of the page that it should redirect to
*
* @return void does not return anything rather it redirects to a page
*/

static public function redirectto($page = NULL){
   if ($page != NULL){
       header("Location: {$page}");
   exit();    
   }
}

/**
* create a unique id
*
* @param string $id the uniquely generated id
*
* @return string returns the unique id
*/

static public function createid(){
   $id = uniqid().uniqid();
   return $id;
}



static public function idfromname($fname = "John", $lname = "doe"){
   $num = uniqid();
   $flen = strlen($fname);
   $llen = strlen($lname);
   
   $id = substr($fname, 0, 2).substr($lname, 0,2).substr(strrev($num), 1, 4);
   return $id;
}

static public function getsessionuser(){
   $user = isset($_SESSION["loggedin"]) ? $_SESSION["loggedin"] : "";
   return $user;
}

static public function fullproductimage($picname){
   $image = "<img src='".HREF."images/products/".$picname."' alt='".$picname."' />";
   return $image;
}

static public function thumbnailimage($picname, $folder){
   $image = "<img src='".HREF."images/".$folder."/thumbnail".$picname."' alt='".$picname."' />";
   return $image;
}

static public function siteimage($picname){
   $image = "<img src='".HREF."images/sitephotos/".$picname."' alt='".$picname."' />";
   return $image;
}

static public function newdatetime(){
   $dateTime = date('Y-m-d H:i:s');
   return $dateTime;
}

static public function newDate(){
   $date = date('Y-m-d');
   return $date;
}

static public function newTime(){
   $time = date('H:i:s A');
   return $time;
}

/**
 * returns a date in the format "Friday 4th May 2012"
 * a date is passed in as an argument and then it converts
 * it into this meaningful and easily readable form
 */
static public function niceDate($data){
   $date = explode("-", $data);
   return date("l jS F Y", mktime(0,0,0,$date[1],$date[2],$date[0]));
}

static public function niceDateTimestamp($data){
   return date("l jS F Y H:i:s", $data);
}

/**
* returns all the get values in a url that can be appended to the end of another get variable
*
* 
*/

static public function currentgetvalues(){
   if (isset($_GET)){
       
       $result = NULL;
           foreach ($_GET as $key => $values){
               if (end($_GET) == $values){
                   $result.=$key."=".$values;
               }else{
                   $result.=$key."=".$values."&";
               }
           }
   }
   else{
    return NULL;
   }
   return $result;
}

static public function currentactpage(){
   $act = (!empty($_GET['act'])) ? $_GET['act'] : NULL;
   return $act;
}

static public function getid(){
   $id = (!empty($_GET['id'])) ? $_GET['id'] : NULL;
   return $id;
}

/**
* function that turns a string to uppercase
*
* @param string $text the text that will be converted
* @return string $capitalized the capitalized version of the string
*/

static public function capitalize($text){
   $capitalized = strtoupper($text);
   return $capitalized;
}

//gets attributes or variables of a class
static function attributes(){
   $class = get_class("userforms");
   return get_class_vars($class);
}

//encrypt the value
static public function encryptvalue($value = NULL){
   if ($value != NULL){
       return sha1($value);
   }
}



static public function encodevalue($value, $type){
   if ($type = "url"){
       $result = urlencode($value);
       return $result;
   }elseif ($type = "html"){
       $result = htmlspecialchars($value, ENT_QUOTES);
       return $result;
   }
}

static public function decodevalue($value, $type){
   //$database = new database;
   if ($type = "url"){
       $result = urldecode($value);
       return $result;
   }elseif ($type = "html"){
       $result = htmlspecialchars_decode($value, ENT_QUOTES);
       return $result;
   }
}

static public function quotes($value){
   $result = htmlspecialchars($value, ENT_QUOTES);
   return $result;
}

/**
* a list of countries
* @param string $countries list of all countries
* @return array returns an array of countries
*/

static public function countries(){
   $countries = "Afghanistan,Akrotiri,Albania,Algeria,American Samoa,Andorra,Angola,Anguilla,Antarctica,Antigua and Barbuda,Argentina,Armenia,Aruba,Ashmore and Cartier Islands,Australia,Austria,Azerbaijan,Bahamas,Bahrain,Bangladesh,Barbados,Bassas da India,Belarus,Belgium,Belize,Benin,Bermuda,Bhutan,Bolivia,Bosnia and Herzegovina,Botswana,Bouvet Island,Brazil,British Indian Ocean Territory,British Virgin Islands,Brunei,Bulgaria,Burkina Faso,Burma,Burundi,Cambodia,Cameroon,Canada,Cape Verde,Cayman Islands,Central African Republic,Chad,Chile,China,Christmas Island,Clipperton Island,Cocos (Keeling) Islands,Colombia,Comoros,Congo (Democratic Republic of the),Congo (Republic of the),Cook Islands,Coral Sea Islands,Costa Rica,Cote d'Ivoire,Croatia,Cuba,Cyprus,Czech Republic,Denmark,Dhekelia,Djibouti,Dominica,Dominican Republic,Ecuador,Egypt,El Salvador,Equatorial Guinea,Eritrea,Estonia,Ethiopia,Europa Island,Falkland Islands (Islas Malvinas),Faroe Islands,Fiji,Finland,France,French Guiana,French Polynesia,French Southern and Antarctic Lands,Gabon,Gambia,Gaza Strip,Georgia,Germany,Ghana,Gibraltar,Glorioso Islands,Greece,Greenland,Grenada,Guadeloupe,Guam,Guatemala,Guernsey,Guinea,Guinea-Bissau,Guyana,Haiti,Heard Island and McDonald Islands,Holy See (Vatican City),Honduras,Hong Kong,Hungary,Iceland,India,Indonesia,Iran,Iraq,Ireland,Isle of Man,Israel,Italy,Jamaica,Jan Mayen,Japan,Jersey,Jordan,Juan de Nova Island,Kazakhstan,Kenya,Kiribati,North Korea,South Korea,Kuwait,Kyrgyzstan,Laos,Latvia,Lebanon,Lesotho,Liberia,Libya,Liechtenstein,Lithuania,Luxembourg,Macau,Macedonia,Madagascar,Malawi,Malaysia,Maldives,Mali,Malta,Marshall Islands,Martinique,Mauritania,Mauritius,Mayotte,Mexico,Federated States of Micronesia,Moldova,Monaco,Mongolia,Montserrat,Morocco,Mozambique,Namibia,Nauru,Navassa Island,Nepal,Netherlands,Netherlands Antilles,New Caledonia,New Zealand,Nicaragua,Niger,Nigeria,Niue,Norfolk Island,Northern Mariana Islands,Norway,Oman,Pakistan,Palau,Panama,Papua New Guinea,Paracel Islands,Paraguay,Peru,Philippines,Pitcairn Islands,Poland,Portugal,Puerto Rico,Qatar,Reunion,Romania,Russia,Rwanda,Saint Helena,Saint Kitts and Nevis,Saint Lucia,Saint Pierre and Miquelon,Saint Vincent and the Grenadines,Samoa,San Marino,Sao Tome and Principe,Saudi Arabia,Senegal,Serbia and Montenegro,Seychelles,Sierra Leone,Singapore,Slovakia,Slovenia,Solomon Islands,Somalia,South Africa,South Georgia and the South Sandwich Islands,Spain,Spratly Islands,Sri Lanka,Sudan,Suriname,Svalbard,Swaziland,Sweden,Switzerland,Syria,Taiwan,Tajikistan,Tanzania,Thailand,Timor-Leste,Togo,Tokelau,Tonga,Trinidad and Tobago,Tromelin Island,Tunisia,Turkey,Turkmenistan,Turks and Caicos Islands,Tuvalu,Uganda,Ukraine,United Arab Emirates,United Kingdom,United States,Uruguay,Uzbekistan,Vanuatu,Venezuela,Vietnam,Virgin Islands,Wake Island,Wallis and Futuna,West Bank,Western Sahara,Yemen,Zambia,Zimbabwe";
   
$countries_array = explode(",", $countries);
return $countries_array;
}

/**
 * returns list of all African countries
 */
static public function africanCountries(){
   $countries = "Algeria, Angola, Ascension, Benin, Botswana, Burkina Faso, Burundi, Cabinda, Cameroon, Cape Verde, Central African Republic, Chad, Comoros , Cueta, Djibouti, Democratic Republic of Congo, Egypt, Equatorial Guinea, Eritrea, Ethiopia, Gabon, Gambia, Ghana, Guinea, Guinea-Bissau, Ivory Coast, Kenya, Lesotho, Liberia, Libya, Madagascar, Madeira, Malawi, Mali, Mauritania, Mauritius, Mayotte, Melilla, Morocco, Mozambique, Namibia, Niger, Nigeria, Republic of the Congo, Reunion, Rwanda, Sahrawi Arab Democratic Republic, Saint Helena, Sao Tome & Principe, Senegal, Seychelles, Sierra Leone, Somalia, Somaliland, South Africa, South Sudan, Sudan, Swaziland, Tanzania, Togo, Tristan da Cunha, Tunisia, Uganda, Western Sahara, Zambia, Zimbabwe";
return explode(", ", $countries);
}

/**
* this is a function to check for valid email addresses
* @return boolean returns true if the email is valid and false if the email is not valid
*/

static public function validemail($email){
   if (preg_match('^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zAZ0-9_-]+)*\.([a-zA-Z]{2,6})$^', $email)){
       return true;
   }elseif (!preg_match('^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zAZ0-9_-]+)*\.([a-zA-Z]{2,6})$^', $email)){
       return false;
   }
}

/**
 * checks if a url is valid
 */
static public function validurl($url){
   if (!empty($_POST['homepage']) && strpos($_POST['homepage'], 'http://') === false && strpos($_POST['homepage'], 'https://') === false) {
    $_POST['homepage'] = 'http://' . $_POST['homepage'];
} 
   $pattern = "#^(http:\/\/|https:\/\/|www\.|//)*(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d{1,5}))?([A-Z0-9_-]|\.|\/|\?|\#|=|&|%)*$#i";
    if (!preg_match($pattern, $url)) {
        return FALSE;
    } else {
        return TRUE;
    }
}

/**
* checks if an input is of a numeric type
*
* @return boolean returns true if it's numeric and false if not
*/ 

static public function numeric($value){
   if (is_numeric($value)){
       return true;
   }elseif (!is_numeric($value)){
       return false;
   }
}


static public function formattedarray($array){
   echo "<pre>";
   print_r($array);
   echo "</pre>";
}


/* here is a function to build a redirect page with a process confirmation variable */

static public function redirectpage(){
   if (isset($_GET)){
       $result = tools::currentgetvalues();
       $page =  $_SERVER['PHP_SELF']."?".$result."&success=true";    
   }else{
       $page = $_SERVER['PHP_SELF'];
   }
   
   return $page;
}

/**
* confirm whether a process was successfully executed or not
* it uses a $_GET variable (ie sucess) to detect whether it was successful or not
* will return if it was and no if it wasnt
*/

static public function confirmprocess(){
   if (isset($_GET['success'])){
       if (tools::capitalize($_GET['success']) == "TRUE"){
           $notice = "The process was successful";
           $type = "success";
       }elseif (tools::capitalize($_GET['success']) == "FALSE"){
           $notice = "The process failed";
           $type="warning";
       }
   return tools::notice($notice, $type);
   }
   
   
}

/**
* shows the current page name (eg. login.php)
*
* @return string returns the name of the current page
*/

static public function showPageName(){
   $path = $_SERVER['PHP_SELF']."<br />";
   $pagename = explode("/", $path);
   $page =  $pagename[count($pagename) - 2];
   return $page;
}

static public function checkFormUploadError($submitButtonName, $fileField){
   $message = NULL;
   if (isset($_POST[$submitButtonName])){
       if (isset($_FILES[$fileField])){
           if ($_FILES[$fileField]['error'] == 0){
               $message = "no errors";
           }
           elseif ($_FILES[$fileField]['error'] == 1){
               $message = "Sorry the file is too large. Try again.";
           }
           elseif ($_FILES[$fileField]['error'] == 2){
               $message = "Sorry the file is too large. Try again.";
           }
           elseif ($_FILES[$fileField]['error'] == 3){
               $message = "Sorry an error occured. Please try again.";
           }
           elseif ($_FILES[$fileField]['error'] == 4){
               $message = "Please upload a file.";
           }
           elseif ($_FILES[$fileField]['error'] == 6){
               $message = "Sorry an error occured. Please try again or contact the web master.";
           }
           elseif ($_FILES[$fileField]['error'] == 7){
               $message = "Sorry an error occured. Please try again or contact the web master.";
           }
           elseif ($_FILES[$fileField]['error'] == 8){
               $message = "Sorry an error occured. Please try again or contact the web master.";
           }
       }
       else{
           $message = NULL;
       }
   }
   else{
       $message = NULL;
   }
   return $message;
}

static public function getFileExtension($file) {
   $explode = explode(".", $file); 
  return end($explode);
}

/**
* this a function that returns the active page
*
* it is used to assign the class - activePage - to an element if the current page equals
* the one provided in the argument
*/

static public function showActivePage($upage){
  
  $page = substr(OR_tools::showPageName(), 0, -4); 
  if (strtoupper($page) == strtoupper($upage)){
       $activePage = " class='activePage'";
  }
  else{
       $activePage = NULL;
  }
   
   return $activePage;
}

}


?>

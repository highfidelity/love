<?php
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved.
//  http://www.lovemachineinc.com

// PHP script to generate the list of words required for the clouder
// In PHP so we can use the config file to get correct db info
// useage is php cloud.gen.php company_id
// the output can then be piped directly into clouder.py
        include("class/frontend.class.php");
        $front = Frontend::getInstance();

  include_once("db_connect.php");
  include_once("autoload.php");
  include("review.php");

        if(!$front->isUserLoggedIn()){
            $front->getUser()->askUserToAuthenticate();
        }


$json_output = false;
$interval = 7;
$wordLength = 15;
$where = " AND private = 0";

if(isset($_GET) && isset($_GET['company_id'])) {
    is_numeric($_GET['company_id']) or die("company_id must be a number\n");
    $company_id = mysql_real_escape_string($_GET['company_id']);
    $json_output = true;
    if(isset($_GET['userdata'])) {
        $where = " AND `receiver` = '{$_SESSION['username']}' ";
        $interval = 60;
        $wordLength = 100;
    }
} else {
    // validate the company_id
    $argc == 2 or die("useage: php cloud.gen.php company_id\n");
    is_numeric($argv[1]) or die("company_id must be a number\n");

    // set the company_id
    $company_id = mysql_real_escape_string($argv[1]);
}

// create the query
$sql = "SELECT l.why from ".LOVE." l WHERE at >= DATE_SUB(CURRENT_DATE, INTERVAL $interval DAY) AND l.company_id = $company_id $where";
$result = mysql_query($sql) or die(mysql_error());

// an array that maps a word to the number of occurences
$counts = array();

// an array with filter words
$filterWords = array();

// current xml tag
$currentTag = "";

// index
$ind = 0;

// check if WordCloudFilter.xml exists and is readable
if(file_exists(dirname(__FILE__).'/WordCloudFilter.xml') && 
   is_readable(dirname(__FILE__).'/WordCloudFilter.xml')) {
       
       // assign the filename
       $filename = dirname(__FILE__).'/WordCloudFilter.xml';
       //create a parser
       $xml_parser  =  xml_parser_create();
       // assing tags handlers
       xml_set_element_handler($xml_parser, "startTag", "endTag");
       // assign content handler
       xml_set_character_data_handler($xml_parser, "contents");
       // read the file
       $data = file_get_contents($filename);
       // parse the file
       xml_parse($xml_parser, $data);
       // free the xml parser
       xml_parser_free($xml_parser); 
}

// for each result, split it in to words, and for each word increase that words count
while ($row = mysql_fetch_array($result))
{
    $words = preg_split("/[^a-zA-Z]+/", $row['why']);
    foreach ($words as $word)
    {
        $word = strtolower(stripslashes(trim($word)));
        // if the word is in our filter array
        // we will not include it in the count
        if(strlen($word) > 1 && strlen($word) < $wordLength && !in_arrayi($word, $filterWords) ){
            // checks if the array index exists and if it does increment the value
            $word = " " . $word;
            if(isset($counts[$word]))
            {
                $counts[$word]++;
            }
            // otherwise initilize the value to 1
            else
            {
                $counts[$word] = 1;
            }
        }
    }
}

arsort($counts);

if($json_output) {
    echo json_encode(array('tags' => $counts));    
} else {
    foreach ($counts as $word => $count)
    {
        echo("$word, $count\n");
    }
}
/**
 * This function is called when the xml parser
 * reaches a start tag
 */
function startTag($parser, $data){
    global $currentTag;
    $currentTag = $data;
}

/**
 * This function is called when the xml parser
 * reaches an end tag
 */
function endTag($parser, $data){
    global $ind;
    if($data == "WORD"){
        $ind++;
    }
}

/**
 * This function is called when the xml parser
 * reads content.
 */
function contents($parser, $data){
    global $currentTag, $filterWords;
    $data = trim($data);
    if($currentTag == "WORD" && strlen($data) > 0){
        $filterWords[] = $data;
    }
}

/**
 * case insensitive version of in_array()
 */
function in_arrayi( $needle, $haystack ) {
    $found = false;
    foreach( $haystack as $value ) {
        if( strtolower( $value ) == strtolower( $needle ) ) {
            $found = true;
        }
    }   
    return $found;
} 

?>

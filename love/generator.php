<?php

/*
// this is a javascript generator
// it is used to generate company_name and/or sandbox specific js files
// it will then output a static file which will be used and cached in the future
// 
// it relies on an htaccess file in the js directory something like the following
// 
        RewriteBase /
        
        # first we handle sanbox redirects
        RewriteCond %{REQUEST_FILENAME} !-s
        RewriteCond %{REQUEST_URI} (~[^\/]*)
        RewriteRule (.*) /%1/love/generator.php?file=$1&sandbox=%1/ [L]
            
        # then we handle non sandbox redirects
        RewriteCond %{REQUEST_FILENAME} !-s
        RewriteCond %{REQUEST_URI} ^[^~]
        RewriteRule (.*) /love/generator.php?file=$1 [L]
// 
// and gets content from a view in the view/js folder
// the view file should be names filename.js.php
// and should build a $js_contents variable which will be output
// both to a file and the screen
// once the generation has happened the htaccess will then
// ensure the flat file version is used
// to reset the file just delete the flat file version in the js folder

*/

// prevent this file from being called directly
// we should only allow it to be accessed via mod_rewrite
if(preg_match('/generator.php/', $_SERVER["REQUEST_URI"])) {
    // 404
    no_file();
}

// we need to check the usual login shtuff
include("class/frontend.class.php");
include_once("helper/check_new_user.php"); 
$front = Frontend::getInstance();

include_once("db_connect.php");
include_once("autoload.php");

if(!$front->isUserLoggedIn()){
    $front->getUser()->askUserToAuthenticate();
}

// what file are we after?
//untaint this paramater
$filename = isset($_GET['file']) ? preg_replace("/[^a-zA-Z0-9\_\-]/","",$_GET['file']) : '';


// does the file exist?
if(file_exists("view/js/$filename.php")) {
    // if so let's include it
    include("view/js/$filename.php");
    // and check it's created the $js_content variable
    if(!is_null($js_contents) || empty($js_contents)) {
        // let's pretend we're a js file if we can
        if(!headers_sent()) {
            header("Content-Type: text/js;");
        }
        // and output the js
        // then write the file so we don't need to do this again
        echo $js_contents;
        // Do not write unique configuration to global file locations (multitenant)
        // Writing to a global file will result in all tenants getting the generated
        // URL from the first tenant that receives the request.
        // This may be solved by using js/{domain.sendlove.us}_$filename and tranform in .htaccess
        // however files would have to be removed every time the source files are updated
         js_write_file('js/' . SERVER_NAME . '_' . $filename, $js_contents);
        // we shouldn't need to do anything else
        die();
    }
    // if there's no $js_contents we'll 404 in a bit
}
// if we've got here there's no valid file to create the js from
// so we'll 404
no_file();

function no_file() {
    // let's cover our tracks with a 404 for any error condition
    if(!headers_sent()) {
        header('HTTP/1.0 404 Not Found');
    }
    echo "<h1>404 Not Found</h1>";
    echo "The page that you have requested could not be found.";
    die();
}

function js_write_file($filename, $contents) {
    // just a simple file writer
    // that dies if it can't open a filehandle
    // if that's the case we'll never cache the file
    // which is a shame but won't break anything
    $fh = fopen($filename, 'w') or die();
    fwrite($fh, $contents);
    fclose($fh);
}

?>

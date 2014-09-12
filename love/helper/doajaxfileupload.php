<?php
include_once('../config.php');
require_once("../class.session_handler.php");
include("check_session.php");

mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD) or die(mysql_error());;
mysql_select_db(DB_NAME) or die(mysql_error());

if(!empty($_SESSION['username']) && empty($_SESSION['nickname']))
{
  // setting up session.
  initSessionData($_SESSION['username']);
}

	$error = "";
	$msg = "";
	$filename = "";
	$imgWidth = 0;
	$imgHeight = 0;

	$fileElementName = 'attachment';
	if (empty($_SESSION['username'])) {
	    $error = 'You need to be logged in to upload a file';
	}
	else if(!empty($_FILES[$fileElementName]['error']))
	{
		switch($_FILES[$fileElementName]['error'])
		{

			case '1':
				$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
				break;
			case '2':
				$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
				break;
			case '3':
				$error = 'The uploaded file was only partially uploaded';
				break;
			case '4':
				$error = 'No file was uploaded.';
				break;

			case '6':
				$error = 'Missing a temporary folder';
				break;
			case '7':
				$error = 'Failed to write file to disk';
				break;
			case '8':
				$error = 'File upload stopped by extension';
				break;
			case '999':
			default:
				$error = 'No error code avaiable';
		}
	}elseif(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none')
	{
		$error = 'No file was uploaded..';
	}else 
	{

			$fileName = $_FILES[$fileElementName]['name'];

			// Get image type.
			// We use @ to omit errors
			@list($imgWidth, $imgHeight, $imtype, ) = getimagesize($_FILES[$fileElementName]['tmp_name']);

			if ($imtype == 3) // cheking image type
			    $ext="png";   // to use it later in HTTP headers
			elseif ($imtype == 2)
			    $ext="jpeg";
			elseif ($imtype == 1)
			    $ext="gif";
			else
			    $error = 'Error: unknown file format';
			
			if($imgWidth > MAX_LOGO_WIDTH || $imgHeight > MAX_LOGO_HEIGHT){

			    $error .= "Image is too big. Max logo width is: ".MAX_LOGO_WIDTH." px. Max logo height is: ".MAX_LOGO_HEIGHT." px";
			}
		      
			// If there was no error
			if (isset($ext) && $error == "") 
			{
				$tmpName = $_FILES[$fileElementName]['tmp_name'];

				// Create the upload directory with the right permissions if it doesn't exist
				$upload_dir = "companydata/".$_REQUEST['company_id'];

				if(!is_dir($upload_dir)){
					mkdir($upload_dir, 0777);
					chmod($upload_dir, 0777);
				}
	    
			$filename = $upload_dir."/".$_FILES[$fileElementName]['name'];
			move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $filename);	  
			}
			
	}

	echo json_encode(array('error' => $error, 'msg' => $msg, 'filename' => $filename, 'width'=> $imgWidth, 'height' => $imgHeight));

?>

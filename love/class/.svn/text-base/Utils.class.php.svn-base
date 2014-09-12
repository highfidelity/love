<?php

class Utils
{

  // This function converts the passed time $x
  // into a user-friendly format - 2 minutes ago etc
  public static function relativeTime($x)
  {
    $plural = '';

    $mins = 60;
    $hour = $mins * 60;
    $day = $hour * 24;
    $week = $day * 7;
    $month = $day * 30;
    $year = $day * 365;
    $dformat = "";

    if($x >= $year){
      $x = ($x / $year)|0;
      $dformat="yr";
    }else if($x >= $month){
      $x = ($x / $month)|0;
      $dformat="mnth";
    }else if($x >= $day){
      $x = ($x / $day)|0;
      $dformat="day";
    }else if($x >= $hour){
      $x = ($x / $hour)|0;
      $dformat="hr";
    }else if($x >= $mins){
      $x = ($x / $mins)|0;
      $dformat="min";
    }else{
      $x |= 0;
      $dformat="sec";
    }
    if($x > 1){
      $plural = 's';
    }
    return $x.' '.$dformat.$plural.' ago';
  }

  public static function getUserInfoByUserId($userid) {
    $sql = "SELECT picture,nickname,username FROM ".USERS." WHERE id = '$userid' LIMIT 0,1";
    $res = mysql_query($sql);
    $retVal = mysql_fetch_assoc($res);
    if ($retVal['picture'] != null) {
      $image = '/uploads/'.$retVal['picture'];
    } else {
      $image = '/images/no_picture.png';
    }
        return array(
                    'image' => $image,
                    'nickname' => $retVal['nickname'],
                    'username' => $retVal['username']
                    );
  }

  public static function getUserImageByUsername($username,$w,$h,$zc) {
    $sql = "SELECT picture FROM ".USERS." WHERE nickname = '$username' LIMIT 0,1";
    $res = mysql_query($sql);
    $retVal = mysql_fetch_assoc($res);
    return self::getUserImageByPicture($retVal['picture'],$w,$h,$zc);
  }
  
  public static function checkVar($var){
      $var = trim($var); 
      if(isset($var) && !empty($var)){
          return true;
      } else {
          return false;
      }
  }
  public static function matchStrings($str1, $str2){
      if(strcmp($str1,$str2) == 0){
          return true;
      } else {
          return false;
      }
  }
  public static function getUserImageByPicture($image = null,$w = 100, $h = 100, $zc = 0) {
    if ($image != null) {
      return self::getImageFromThumbs($image,$w,$h,$zc);
    } else {
        return 'thumb.php?t=gUIBP&src=/images/no_picture.png&w='.$w.'&h='.$h.'&zc='.$zc;
    }
  }

    public static function checkForNewUser($user_id){
        // check if user is in out database
        $query = "SELECT lastlogin FROM " . USERS . " WHERE id=" .intval($user_id);
        $res = mysql_query($query) or error_log("check4NU: $query : ".mysql_error());
		
		if (!$res || mysql_num_rows($res) !== 1) {
			$message  = "checkForNewUser($user_id)\n";
			$message .= 'If Mysql Error: ' . mysql_error() . "\n";
			$message .= 'Whole query: ' . $query;
			error_log($message);
			return true;
		}
		
		$row=mysql_fetch_object($res) or error_log("check4NUfetch: ".mysql_error());

		if(!strcmp((string)$row->lastlogin, "0000-00-00 00:00:00")){
			$query = "UPDATE " . USERS . " SET lastlogin=NOW() WHERE id=" .intval($user_id);
			$res = mysql_query($query);
			return true;
		}
		else {
			return false;
		}		
    }

    public static function setUserSession($id, $username, $nickname, $admin){
        $_SESSION["userid"]   = (int)$id;
        $_SESSION["username"] = $username;
        $_SESSION["nickname"] = $nickname;
        $_SESSION["admin"]    = $admin;
        $_SESSION["new_user"] = self::checkForNewUser($id);
    }
    
    public static function getImageDate($image){
        $app_root_path = dirname(__FILE__);
        $app_root_path = str_replace('class','',$app_root_path);
        require_once ($app_root_path . 'class/Assets.class.php');
        
        if(class_exists('Assets')){
            
            $assetManager = new Assets();
            if($assetManager->imageExists($image)){
                return strtotime($assetManager->getUpdated());
            } else {
                return strtotime("0000-00-00 00:00:00");
            }
        } else {
            return strtotime("0000-00-00 00:00:00");
        }
    }
    
    public static function getImageFromThumbs($image, $w=50, $h=50, $zc=0){
        return SERVER_URL . "thumb.php?t=gIFT&src=".$image."&w=".$w."&h=".$h."&zc=".$zc."&v=".self::getImageDate($image);
    }

    // Convinient function to that returns true if the passed
    // user is and admin, or false if not.
    /*public static function getUserAdminStatus($id) {
        // Call the login API and retrieve the user info
        $url = SERVER_URL . 'loginApi.php';
        
        $params = array(
            'action'=>'getuserdata',
            'user_id'=>$id
        );
        
        // Do request
        ob_start();
        CURLHandler::Post($url, $params, false, true);
        
        // Get response
        $response = ob_get_contents();
        ob_end_clean();
        
        // Decode response
        $ret = json_decode($response);
        
        // Check for errors
        if ($ret->error == 1) {
            return false;
        } else {
            return true;
        }
        
        var_dump($ret);
    }*/
}

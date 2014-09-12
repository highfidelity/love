<?php

class Utils {
     
     // This function converts the passed time $x
     // into a user-friendly format - 2 minutes ago etc
     public static function relativeTime($x) {
          $plural = '';
          
          $mins = 60;
          $hour = $mins * 60;
          $day = $hour * 24;
          $week = $day * 7;
          $month = $day * 30;
          $year = $day * 365;
          $dformat = "";
          
          if ($x >= $year) {
               $x = ($x / $year) | 0;
               $dformat = "yr";
          } else if ($x >= $month) {
               $x = ($x / $month) | 0;
               $dformat = "mnth";
          } else if ($x >= $day) {
               $x = ($x / $day) | 0;
               $dformat = "day";
          } else if ($x >= $hour) {
               $x = ($x / $hour) | 0;
               $dformat = "hr";
          } else if ($x >= $mins) {
               $x = ($x / $mins) | 0;
               $dformat = "min";
          } else {
               $x |= 0;
               $dformat = "sec";
          }
          if ($x > 1) {
               $plural = 's';
          }
          return $x . ' ' . $dformat . $plural . ' ago';
     }
     
     public static function getCountryList($ab = null) {
          global $countrylist;
          $output = "";
          if (isset($ab)) {
               foreach ( $countrylist as $abbr => $country ) {
                    $selected = $ab == $abbr ? " selected" : "";
                    $output .= '<option value="' . $abbr . '"' . $selected . '>' . $country . '</option>';
               }
          } else {
               foreach ( $countrylist as $abbr => $country ) {
                    $output .= '<option value="' . $abbr . '">' . $country . '</option>';
               }
          }
          return $output;
     }
     public static function getProviderList($country_abbr, $selected_provider = null) {
          global $smslist;
          $output = "";
          foreach ( $smslist[$country_abbr] as $provider => $sms ) {
               if (isset($selected_provider) && $provider == $selected_provider) {
                    $output .= '<option value="' . $provider . '" selected>' . $provider . '</option>';
               } else {
                    $output .= '<option value="' . $provider . '">' . $provider . '</option>';
               }
          }
          return $output;
     }
     public static function getUserInfoByUserId($userid) {
          $sql = "SELECT picture,nickname,username FROM " . LOVE_USERS . " WHERE id = '$userid' LIMIT 0,1";
          $res = mysql_query($sql);
          $retVal = mysql_fetch_assoc($res);
          if ($retVal['picture'] != null) {
               $image = '/uploads/' . $retVal['picture'];
          } else {
               $image = '/images/no_picture.png';
          }
          return array('image' => $image, 'nickname' => $retVal['nickname'], 'username' => $retVal['username']);
     }
     
     public static function getUserImageByUsername($username) {
          #$db = new Database();
          $sql = "SELECT picture FROM " . LOVE_USERS . " WHERE nickname = '$username' LIMIT 0,1";
          $res = mysql_query($sql);
          $retVal = mysql_fetch_assoc($res);
          if ($retVal['picture'] != null) {
               return '/uploads/' . $retVal['picture'];
          } else {
               return '/images/no_picture.png';
          }
     }
     
     public static function checkVar($var) {
          $var = trim($var);
          if (isset($var) && ! empty($var)) {
               return true;
          } else {
               return false;
          }
     }
     public static function matchStrings($str1, $str2) {
          if (strcmp($str1,$str2) == 0) {
               return true;
          } else {
               return false;
          }
     }
     public static function getUserImageByPicture($image = null, $w = 100, $h = 100, $zc = 0) {
          if ($image != null) {
               return 'thumb.php?t=rGIBP&src=/uploads/' . $image . '&w=' . $w . '&h=' . $h . '&zc=' . $zc;
          } else {
               return 'thumb.php?t=rGIBPn&src=/images/no_picture.png&w=' . $w . '&h=' . $h . '&zc=' . $zc;
          }
     }
     
     public static function checkForNewUser($user_id) {
          
          // check if user is in our database
          $query = "SELECT `id` FROM " . REVIEW_USERS . " WHERE id='" . intval($_SESSION['userid']) . "'";
          $res = mysql_query($query);
          
          // empty result
          if (! mysql_num_rows($res) > 0) {
               return true;
          }
          return false;
     }
     
     public static function setUserSession($id, $username, $nickname, $admin) {
          $_SESSION["userid"] = $id;
          $_SESSION["username"] = $username;
          $_SESSION["nickname"] = $nickname;
          $_SESSION["admin"] = $admin;
          $_SESSION["new_user"] = self::checkForNewUser($id);
     }

}

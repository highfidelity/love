<?php
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com
if ($_SERVER['PHP_SELF'] == __FILE__) die("Invalid request"); 

    function checkReferer() {
        if (empty($_SERVER['HTTP_REFERER'])) {
            return false;
        }

        $names = array(SERVER_NAME);
        if (preg_match('/^www\.(.*)$/', SERVER_NAME, $matches)) {
            $names[] = $matches[1];
        }

        foreach ($names as $name) {
            $len = strlen($name);
            if (   substr($_SERVER['HTTP_REFERER'], 0, $len + 7) == 'http://'.$name
                || substr($_SERVER['HTTP_REFERER'], 0, $len + 8) == 'https://'.$name) {
                return true;
            }
        }

        return false;
    }

    /* enforceRateLimit
     *
     * Used to impose rate limiting on various operations.
     *
     * Parameters:
     *   class   - the type of operation being rate limited (e.g. 'love')
     *   id      - unique identifying who/what is being rate limited (e.g. a user or a company id)
     *   cost    - the cost of each operation (in seconds)
     *   maximum - the maximum combined cost tolerated for a set of operations (unit of decay is seconds)
     *
     * Returns:
     *   # seconds until rate limit expires, or 0 if no rate limit
     */
    function enforceRateLimit($class, $id, $test=false) 
    {
        $classMap = array('love'=>array('cost'=>15, 'maximum'=>20));

        if (!isset($classMap[$class])) return 0;
        $cost = $classMap[$class]['cost'];
        $maximum = $classMap[$class]['maximum'];

        $qry = "select TIMESTAMPDIFF(SECOND,NOW(),expires) as expires from ".LIMITS." where class='$class' and id='$id'";
        $res = mysql_query($qry);
		if ($res && ($row = mysql_fetch_assoc($res))) {
            $expires = max(0, $row['expires']);

            if ($expires > $maximum) {
error_log("RateLimitTrigger: $class, $id, $expires");
                return $expires - $maximum;
            }
        } else {
            $expires = 0;
        }

        if (!$test) {
            $expires += $cost;
            $res = mysql_query("update ".LIMITS." set expires=TIMESTAMPADD(SECOND,$expires,NOW()) where class='$class' and id='$id'");
            if (!$res) {
                $res = mysql_query("insert into ".LIMITS." set class='$class', id='$id', expires=TIMESTAMPADD(SECOND,$expires,NOW())");
            }
        }
 
        return 0;
    }

    function getNickName($username) {
        static $map = array();
        if (!isset($map[$username])) {
            $strSQL = "select nickname from ".USERS." where username='".$username."'";
            $result = mysql_query($strSQL);
            $row    = mysql_fetch_array($result);
            $map[$username] = $row['nickname'];
        }
        return $map[$username];
    }
    
    function getPicture($username) {
    	$query = 'SELECT `picture` FROM `' . USERS . '` WHERE `username` = "' . mysql_real_escape_string($username) . '" LIMIT 1;';
    	$result = mysql_query($query);
    	if (!$result || (mysql_num_rows($result) == 0)) {
			return SERVER_URL . 'thumb.php?t=gPn&src=/images/no_picture.png&h=' . PROFILE_PICTURE_HEIGHT . '&w=' . PROFILE_PICTURE_WIDTH . '&zc=0';
		} else {
			$row = mysql_fetch_assoc($result);
			if (empty($row['picture'])) {
				return SERVER_URL . 'thumb.php?t=gPp&src=/images/no_picture.png&h=' . PROFILE_PICTURE_HEIGHT . '&w=' . PROFILE_PICTURE_WIDTH . '&zc=0';
			} else {
				return SERVER_URL . 'thumb.php?t=gPs&src=/uploads/' . $row['picture'] . '&h=' . PROFILE_PICTURE_HEIGHT . '&w=' . PROFILE_PICTURE_WIDTH . '&zc=0';
			}
		}
    }

    function getUserName($nickname) {
        static $map = array();
        if (!isset($map[$nickname])) {
            $strSQL = "select username from ".USERS." where nickname='".$nickname."'";
            $result = mysql_query($strSQL);
            $row    = mysql_fetch_array($result);
            $map[$nickname] = $row['username'];
        }
        return $map[$nickname];
    }

    function getAllNicknames() {
	    $result = array();
	    $strSQL = "select nickname from ".USERS." ORDER BY LENGTH(nickname) DESC";
        $sqlRs = mysql_query($strSQL);
        while ($row = mysql_fetch_array($sqlRs)) {
		    if (!empty($row['nickname'])) array_push($result, $row['nickname']);
        }
        return $result;
    }

    /* initSessionData
     *
     * Initializes the session data for a user.  Takes as input either a username or a an array containing
     * data from a row in the users table.
     */
    function initSessionData($user) {
        if (!is_array($user)) {
            $res = mysql_query("select * from ".USERS." where username='".mysql_real_escape_string($user)."'");
            $user_row = (($res) ? mysql_fetch_assoc($res) : null);
            if (empty($user_row)) return;
        } else {
            $user_row = $user;
        }

        $_SESSION['username']           = $user_row['username'];
        $_SESSION['userid']             = $user_row['id'];
        $_SESSION['confirm_string']     = $user_row['confirm_string'];
        $_SESSION['nickname']           = $user_row['nickname'];
        if ($user_row['company_confirm'] >= '1' || ($user_row['company_admin'] >= '1')) {
            $_SESSION['company_id']     = $user_row['company_id'];
            $_SESSION['company_admin']  = $user_row['company_admin'];
        }
		else {
			unset($_SESSION['company_id']);
			unset($_SESSION['company_admin']);
		}
        $_SESSION['features'] = intval($user_row['features']) & FEATURE_USER_MASK;
    }

    function isEnabled($features) {
        if (empty($_SESSION['features']) || ($_SESSION['features'] & $features) != $features) {
            return false;
        } else {
            return true;
        }
    }

    function isSuperAdmin() {
        if (empty($_SESSION['features']) || ($_SESSION['features'] & FEATURE_SUPER_ADMIN) != FEATURE_SUPER_ADMIN) {
            return false;
        } else {
            return true;
        }
    }
    
    /*
     * If logged in calculates how much love you've sent this week and if you belong to a company calculates the average love sent by people from your company.
     * 
     * @return Returns an array which contains the company_average and love_sent
     */
    function weeklyLoveCount(){
        $message = Array('love_sent' => 0, 'company_average' => 0);
        $average = 0;
		
    	//Check if user is logged in
    	if(isset($_SESSION['userid'])){
	    	$query = "SELECT COUNT(*) as count FROM ".LOVE." WHERE at >= NOW() - INTERVAL 1 WEEK AND giver = '" . $_SESSION['username'] . "'";  
	    	$result = mysql_query($query);
    		
    		$row = mysql_fetch_array($result);
    		
	    	$message['love_sent'] =  $row['count'];
			
	    	// Check if user is part of a company
	    	if(isset($_SESSION['company_id'])){
	    		// Get the Avg for the company this week
	    		$query = "SELECT COUNT(*) as count FROM ".LOVE." as lv JOIN ".USERS." as u ON lv.giver = u.username ".
				         "WHERE at >= NOW() - INTERVAL 1 WEEK AND u.company_id = " . $_SESSION['company_id'];
	    		$result = mysql_query($query);    		
    			$row = mysql_fetch_array($result);
    			
    			// Total number of love sent    			
    			$num_loves_sent = $row['count'];
    			
    			// Get number of unique users from your company
    			$query = "SELECT COUNT(id) as count FROM ".USERS." WHERE company_id='" . $_SESSION['company_id'] . "'";
    			$result = mysql_query($query);    		
    			$row = mysql_fetch_array($result);
    			
    			//Number of givers from company sending love
    			$num_givers = $row['count'];
    			
    			//Make sure number of love isn't 0
    			if($num_loves_sent > 0){
    				$average = ceil($num_loves_sent / $num_givers);
    			}    			
	    		
	    	}
	    	
	    	$message['company_average'] = $average;	    		    	
    	}
    	
    	return $message;
    }

    function postRequest($url, $post_data) {
        if (!function_exists('curl_init')) {
            error_log('Curl is not enabled.');
            return 'error: curl is not enabled.';
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    function relativeTime($time, $segmentsToReturn = 0) {
      $secs = abs($time);
      $mins = 60;
      $hour = $mins * 60;
      $day = $hour * 24;
      $week = $day * 7;
      $month = $day * 30;
      $year = $day * 365;

      // years
      $segments = array();
      $segments['yr']   = intval($secs / $year);
      $secs %= $year;
      // month
      $segments['mnth'] = intval($secs / $month);
      $secs %= $month;
      if (!$segments['yr']) {
          $segments['day']  = intval($secs / $day);
          $secs %= $day;
          if (!$segments['mnth']) {
              $segments['hr']   = intval($secs / $hour);
              $secs %= $hour;
              if (!$segments['day']) {
                  $segments['min']  = intval($secs / $mins);
                  $secs %= $mins;
                  if (!$segments['hr'] && !$segments['min']) {
                      $segments['sec']  = $secs;
                  }
              }
          }
      }

      $relTime = '';
      $segmentCount  = 0;
      foreach ($segments as $unit=>$cnt) {
          if ($segments[$unit]) {
              $relTime .= "$cnt $unit";
              if ($cnt > 1) {
                  $relTime .= 's';
              }
              $relTime .= ', ';
              $segmentCount++;
              if($segmentsToReturn > 0 && $segmentCount >= $segmentsToReturn) {
                 break;
              }
          }
      }
      $relTime = substr($relTime, 0, -2);
      if (!empty($relTime)) {
          return ($time < 0) ? "$relTime ago" : "in $relTime";
      } else {
          return "just now";
      }
  }

/* Make sure the @str is a UTF-8 string, if not encode it.
 */
function setEncoding($str) {
    if (mb_detect_encoding($str) != "UTF-8") {
        return utf8_encode($str);
    } else return $str;
}

/* avoid <555 error in strip_tags
*/
function smart_strip_tags($html){
    // replace <nnn that is not an html tag
    $pattern = '/<(\d+) /i';
    $tag_jo = 'JOANNE_JOANNE';
    $replacement = $tag_jo . '${1} ';
    $for_stripped = preg_replace($pattern, $replacement, $html);
    // Remove html tags and filter For string
    $for_stripped = strip_tags($for_stripped);
    // put back <nnn
    $pattern = '/' . $tag_jo . '(\d+) /i';
    $replacement = '<${1} ';
    $for_stripped = preg_replace($pattern, $replacement, $for_stripped);
    return $for_stripped;
}

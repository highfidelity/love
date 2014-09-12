<?php

class Love
{
	protected $loveHistoryPageLimit;
	protected $limit;
	protected $overlays;
	
	public $pages;
        const GIVER = 'giver';
        const RECEIVER = 'receiver';
	
	public function setLimit($limit)
	{
		$this->limit = (int)$limit;
		return $this;
	}
	
	public function getLimit()
	{
		return $this->limit;
	}

	public function getOverLays()
	{
		return $this->overlays;
	}

	public function __construct(array $options = null)
	{
        if (is_array($options)) {
            $this->setOptions($options);
        }
        return $this;
	}

// This function returns the
// 10 users who were the receivers of
// most love messages
// If the current user is not among them
// an 11th element is added to the end
// showing the current position of the user
public function getMostLoved($interval = 7) {
    $front = Frontend::getInstance();
    $mycompany=$front->getCompany()->getId()||MAIN_COMPANY;
    $sql =  "SELECT ".USERS.".id, ".USERS.".nickname,".USERS.".picture, count(love.receiver) as love ".
            "FROM ".LOVE." AS love ".
            "INNER JOIN ".USERS." ". 
            "ON love.receiver = ".USERS.".username ".
            "WHERE 1 " .
            "AND love.at >= DATE_SUB(CURRENT_DATE, INTERVAL $interval DAY) AND love.company_id = ".$mycompany." ".
            "GROUP BY love.receiver ".
            "ORDER BY love DESC, love.at DESC";
    $res = mysql_query($sql) or error_log('Unable to get most loved:'.mysql_error()."\n".$sql);
    $pos = 1;
    $found = false;
    $output = "";
    $output1 = array();
    $output2 = array();
    $img = '<img src ="%s" width="14" height="14" />';
    // loop through users
    while($row = mysql_fetch_assoc($res)) {
        $nickname = $row["nickname"];
        // shrink long nicknames to not break layout
        if(strlen($row["nickname"]) > MAX_DISPLAY_NICKNAME_CHARS) {
            $nickname = substr($row["nickname"], 0, MAX_DISPLAY_NICKNAME_CHARS) . MAX_DISPLAY_NICKNAME_REPLACE;
        }

        // make nickname a link to the page with user info
        $nickname = $this->formatUserLink($row["nickname"], $nickname);

        // stop if we have found logged in user and output the 10 most loved
        if ($pos > 10 && $found === true) {
            break;
        }
        // different handling for the current user
        if ($row["id"] == $front->getUser()->getId()) {
            // 10 users output already, attach current user
            if ($pos > 10) {
                $output2[] = "<li class=\"me even full\">$pos. ".sprintf($img,Utils::getUserImageByPicture($row["picture"],14,14))."$nickname</li>".
                                "<div class='tooltip'>".$this->getOverlay($row["nickname"]).'</div>';
            } else {
                if($pos > 5) {
                    $output2[] = "<li class=\"me even\">$pos. ".sprintf($img,Utils::getUserImageByPicture($row["picture"],14,14))."$nickname</li>".
                                "<div class='tooltip'>".$this->getOverlay($row["nickname"]).'</div>';
                } else {
                    $output1[] = "<li class=\"me\">$pos. ".sprintf($img,Utils::getUserImageByPicture($row["picture"],14,14))."$nickname</li>".
                                "<div class='tooltip'>".$this->getOverlay($row["nickname"]).'</div>';
                }
            }
            // set found true to exit loop
            $found = true;
        } else if($pos <= 10) {
            if ($pos > 5) {
                // right column
                $output2[] = "<li class=\"even\">$pos. ".sprintf($img,Utils::getUserImageByPicture($row["picture"],14,14))."$nickname</li>".
                             "    <div class='tooltip'>".$this->getOverlay($row["nickname"]).'</div>';
            } else {
                // left column
                $output1[] = "<li>$pos. ".sprintf($img,Utils::getUserImageByPicture($row["picture"],14,14))."$nickname</li>".
                                "<div class='tooltip'>".$this->getOverlay($row["nickname"]).'</div>';
            }
        }
        $pos++;
    }
    // logged in user still not found
    if (! $found) {
        $nickname = $this->formatUserLink($front->getUser()->getNickname());

        if ($pos > 10) {
            $output2[] = "<li class=\"me even full\">$pos. ".sprintf($img,Utils::getUserImageByPicture($front->getUser()->getPhoto(),14,14)).$nickname."</li>".
                         "<div class='tooltip'>".$this->getOverlay($front->getUser()->getNickname()).'</div>';
        } else if ($pos > 5) {
            $output2[] = "<li class=\"me even\">$pos. ".sprintf($img,Utils::getUserImageByPicture($front->getUser()->getPhoto(),14,14)).$nickname."</li>".
                         "<div class='tooltip'>".$this->getOverlay($front->getUser()->getNickname()).'</div>';
        } else {
            $output1[] = "<li class=\"me\">$pos. ".sprintf($img,Utils::getUserImageByPicture($front->getUser()->getPhoto(),14,14)).$nickname."</li>".
                         "<div class='tooltip'>".$this->getOverlay($front->getUser()->getNickname()).'</div>';
        }
    }
    // now loop through our generated output
    for ($i = 0; $i < 5; $i++) {
        if ( isset($output1[$i]) ) {
            $output .= $output1[$i]; 
        } elseif ($i < 5) {
            $output .= '<li class="nodata">&nbsp;</li>' . "<div class='tooltip'></div>";
        }
        if($pos > 11) {
            if( isset($output2[$i]) && $i != (count($output2)-1)) {
                $output .= $output2[$i];
            } else {
                $output .= "<li class=\"even nodata\">&nbsp;</li>". "<div class='tooltip'></div>";
            }
        } else {
            if ( isset($output2[$i]) ) {
                $output .= $output2[$i];
            } else {
                $output .= "<li class=\"even nodata\">&nbsp;</li>". "<div class='tooltip'></div>";
            } 
        }
    }
        
    if($pos > 11) {
         $output .= $output2[count($output2) - 1];
    }
    return $output;
}

	// This function gets all company love messages
	// that the current user is member of and displays
	// them in a table
	public function getLoveHistory($user, $page, $justUser = false, $when = true)
	{
		$front = Frontend::getInstance();
                $mycompany=$front->getUser()->getCompany_id()||MAIN_COMPANY;
		$page--;
		$l = $this->getLimit() * $page;
		if ($l < 0) {$l=0;}
		$where = '';
                $order_by = ($when == "true") ? " ORDER BY id DESC " : " ORDER BY id ASC ";

		// query to count company love including user private love
		$sql = "SELECT count(*) FROM " . LOVE
                ." WHERE private=0 OR giver='".$user."' OR receiver='".$user."'";
		$res = mysql_query($sql);
		$row = mysql_fetch_row($res);
		$loves = $row[0];

                // if username is different from user that is currently logged in
                // - show only public love for user
                $private_flag = ($user != $front->getUser()->getUsername()) ? ' AND private = 0 ' : '';

                // query to count user love
		$sql = "SELECT count(*) FROM " . LOVE
                ." WHERE (receiver = '$user' OR giver = '$user') ";
                $sql .= $justUser ? ''.$private_flag :
    	           "OR company_id = ".$mycompany." AND private=0 ";              
   		$sql .= $where . $order_by;
		$res = mysql_query($sql);
		$row = mysql_fetch_row($res);
		$count = $row[0];
		$cPages = ceil($count/$this->getLimit());

		$sql = "SELECT id,giver,receiver,why,private,TIMESTAMPDIFF(SECOND,at,NOW()) as delta ".
               "FROM ".LOVE
               ." WHERE (receiver = '$user' OR giver = '$user') ";
                $sql .= $justUser ? ''.$private_flag :
		           "OR company_id = ".$mycompany." AND private=0 OR giver='".$user."' OR receiver='".$user."' ";
		$sql .= $where. $order_by .
               "LIMIT ".$l.",".$this->getLimit();
		$res = mysql_query($sql) or error_log('GetLoveHistory;'.mysql_error()."\n".$sql);


		// Construct json for history
		$this->pages = array(array($page, $cPages, number_format($loves)));
		if (isset($res) && mysql_num_rows($res)===0) {
			return array();
		}
		$history = array();
		for ($i = 1; $row=mysql_fetch_assoc($res); $i++){
			$givernickname = getNickName($row['giver']);
			$givernickname = (!empty($givernickname))?($givernickname):($row['giver']);

			$receivernickname = getNickName($row['receiver']);
			$receivernickname = (!empty($receivernickname))?($receivernickname):($row['receiver']);

			$why = $row['why'];
			if ($row['private']) $why .= " (love sent quietly)";

			$history[] = array(
            "id"               => $row['id'], 
            "giver"            => $row['giver'], 
            "giverNickname"    => $givernickname, 
            "receiver"         => $row['receiver'], 
            "receiverNickname" => $receivernickname, 
            "why"              => stripslashes($why), 
            "delta"            => Utils::relativeTime($row['delta']));
		}
		return $history;
	}

	public function getLoveList($user, $page, $justUser = false, $when = true)
	{
		$els = $this->getLoveHistory($user, $page, $justUser, $when);
		if(empty($els)) {return; }
		$output = "";
		$i = 0;
		foreach($els as $el){
		    if($i == 1) {
		        $class = 'rowodd love_' . $el['id'];
		        $i = 0;
		    } else {
		        $class = 'roweven love_' . $el['id'];
		        $i = 1;
		    }
            if (isset($el['why'])) {
              $why = $el["why"];
            
              if (mb_detect_encoding($why) != "UTF-8") {
                $why = mb_convert_encoding($why, 'UTF-8', mb_detect_encoding($why)); 
              }
            } else {
              $why = '';
            }
            
			$output .= "<tr class=\"$class\">";
			$output .= "<td class=\"headFrom\">";
			$output .= (isset($el["giverNickname"]) ? $this->formatUserLink($el["giverNickname"]) : '');
			$output .= "</td>";
			$output .= "<td class=\"headTo\">";
			$output .= (isset($el["receiverNickname"]) ? $this->formatUserLink($el["receiverNickname"]) : '');
			$output .= "</td>";
			$output .= "<td class=\"headFor\">";
			$output .= html_entity_decode(htmlentities($why,ENT_QUOTES,"UTF-8"),ENT_QUOTES,"UTF-8");
			$output .= "</td>";
			$output .= "<td class=\"headWhen\">";
			$output .= (isset($el["delta"]) ? $el["delta"] : '');
			$output .= "</td>";
			#$front = Frontend::getInstance();
			#if ((!$justUser) && ($front->getUser()->getCompany_admin())) {
			#	$output .= '<td class="headDelete">';
			#	$output .= '<input type="checkbox" value="' . $el['id'] . '" />';
			#	$output .= '</td>';
			#}
			$output .= "</tr>";
		}
		return $output;
	}
	
    // generates the pager for love list table
    public function getListPager($page)
    {
        $output = '<ul class="pager">';
        $iPage=0;
            $iPage = max(0, $page - 5 );
        if ($iPage + 5 > $this->pages[0][1]) {
            $iPage = max(0, $this->pages[0][1] - 5 );
        }
        $iPageStart = $iPage;
        if($page != 1){
            $output .= '<li class="firstPage">&lt;&lt;</li>';
            $output .= '<li class="prev">&lt;</li>';
        }
        while ($iPage < $this->pages[0][1]) {
            if ($iPage > $iPageStart + 9 && $iPage+1 != $this->pages[0][1]) {
                $output .= '<li class="morePage">...</li>';
                break;
            }
            if($page == $iPage+1){
                $output .= '<li class="page current">' . ($iPage+1) . '</li>';
            } else {
                $output .= '<li class="otherPage">' . ($iPage+1) . '</li>';
            }
            $iPage++;
        }
        if($page != $this->pages[0][1]){
            $output .= '<li class="next">&gt;</li>';
            $output .= '<li class="lastPage" lastPage="'.$this->pages[0][1].'">&gt;&gt;</li>';
        }
        $output .= "</ul>";
        return $output;
    }
	
	/**
     * Automatically sets the options array
     * Array: Name => Value
     *
     * @param array $options
     * @return User $this
     */
  private function setOptions(array $options) {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
  }

  public function getOverlay($username) {
    $trend = $this->getTrend($username);
    // we don't want to see 0 next to the slash
    if (!$trend || $trend == 0) {
        $trend = '';
    }
    return '
        <div class="lb">
            <div style="text-align: center;">'.$username.'\'s Stats</div>
            <div class="left">
                <div id="userImage100">
                    <img src="'.Utils::getUserImageByUsername($username,100,100,0).'" width="100" height="100" alt="profile" />
                </div>  
            </div>
            <div class="right">
                <div class="ranking">
                    <!-- temporarily disabled <img src="img/tooltip/'.$this->getTrendImage($trend).'" title="Last week\'s position"  width="20px"/> <span class="chartlabel">'.(abs($trend) == 0 ? '' : abs($trend)).'</span><br> -->
                    <img src="img/tooltip/weekson.png" title="Weeks on chart" width="20px"/> <span class="chartlabel"> '.$this->getWeeksOnChart($username).'</span><br>
                    <img src="img/tooltip/peak.png" title="Top Position" width="20px"/> <span class="chartlabel">'. $this->getTopPosition($username).'</span><br>
                </div>
            </div>
        </div>';
  }

/**
 * Return the user's change in ranking across the current and previous week
 *
 * @author LoveMachine
 * @copyright LoveMachine, LLC 2010
 * @access public
 * @param $username string The username for which to return the trend
 * @return int The trend as a negative or positive number
 */  
  public function getTrend($username){
      $this_week = (int)$this->getWeekRank($username, $week='week(CURRENT_DATE)');
      $last_week = (int)$this->getWeekRank($username, $week='week(CURRENT_DATE)-1');

      if ($this_week === 0) {
          // no love this week? :( they've dropped down completely from their previous rank
          return -$last_week;
      } elseif ($last_week === 0) {
          // no love last week
          return 0;
      }
      
      $trend = -($this_week - $last_week);
      return $trend;
  }

/**
 * Return the icon to be used for a given trend
 *
 * @author LoveMachine
 * @copyright LoveMachine, LLC 2010
 * @access public
 * @param $trend int The trend as a negative or positive number
 * @return string The name of the icon image to use
 */  
  public function getTrendImage($trend) {
      if ($trend < 0) {
          return 'minus.png';
      } else if ($trend > 0) {
          return 'plus.png';
      } else {
          return 'slash.png';
      }
  }
  
  public function getWeekRank ($username, $week='week(CURRENT_DATE)-1', $year='') {
    $front =Frontend::getInstance();

    // if year not provided, set current year
    if ($year == '') {
      $year = date('Y');
    }
    
    // set a variable which is incremented as rows are found, in order to give rank
    $sql = "SELECT rank FROM (
                SELECT @num := @num + 1 rank, rank.nickname FROM (
                    SELECT users.id, (@num := 0) d, users.nickname, WEEK(love.at) AS wn, YEAR(love.at) AS yr, count(love.receiver) AS love
                    FROM ".LOVE." AS love
                    INNER JOIN ".USERS." AS users ON love.receiver = users.username
                    WHERE WEEK(love.at) = ($week)
                    AND love.company_id = ".MAIN_COMPANY."
                    GROUP BY wn, love.receiver
                    ORDER BY wn, COUNT(love.receiver) DESC
                ) AS rank
            ) as rank WHERE nickname='".$username."'";
               
    $res = mysql_query($sql);
    $row = mysql_fetch_assoc($res);
    return $row['rank'];
  }
  

    public function getCurrentRank($username, $interval = 7){

        $front =Frontend::getInstance();
        $mycompany=$front->getCompany()->getId()||MAIN_COMPANY;

        mysql_query("SET @RNUM = 0");
        $sql = "SELECT * FROM (
                    SELECT @RNUM :=  @RNUM +1 row_number, users.id, users.nickname, count(love.receiver) AS love
                    FROM ".LOVE." AS love
                    INNER JOIN ".USERS." AS users ON love.receiver = users.username
                    WHERE love.at >= DATE_SUB(CURRENT_DATE, INTERVAL $interval DAY)
                    AND love.company_id = " . (int) $mycompany . "
                    GROUP BY love.receiver
                    ORDER BY love DESC, love.at DESC
                ) AS rank WHERE rank.nickname='$username'";
        $res = mysql_query($sql);
        $row = mysql_fetch_assoc($res);
        return $row['row_number'];
    }

	public function getWeeksOnChart($username)
	{
		$sql = "SELECT users.id, users.nickname, WEEK(love.at) AS weeknumber, YEAR(love.at) AS yr, COUNT(love.receiver) AS love 
						FROM ".LOVE." AS love 
						INNER JOIN ".USERS." AS users ON love.receiver = users.username 
						WHERE users.nickname='$username' 
						AND love.company_id = ".MAIN_COMPANY." 
						GROUP BY weeknumber 
						ORDER BY yr DESC, weeknumber DESC";
		$res = mysql_query($sql);
		$i = 0;
		$tmpwk = array();
		$weekcount = 1;
		while( $row = mysql_fetch_assoc($res) ){
			$tmpwk[$i] = $row['weeknumber'];
			if($i > 0) {
				if($tmpwk[$i-1] == ($row['weeknumber']+1) ) {
					$weekcount++;
				} else {
					break;
				}
			}
			#echo 'i='.$i.'__ '.$tmpwk[$i-1].' : '.($row['wn']+1).'<br>';
			#var_dump($tmpwk[$i]);
			$i++;
		}
		return $weekcount; #if the latest week is the actual week
	}
	
    public function getTopPosition($username)
    {
        $user_weeks = $this->getUserWeeks($username);

        if(count($user_weeks) > 0){

            // get user rank for every week
            $ranks = array();
            foreach($user_weeks as $user_week){

                // do not include current week as it is covered by 7 days period in getCurrentRank()
                if(date("W") != $user_week['weeknumber']){
                    $ranks[] = $this->getWeekRank($username, $user_week['weeknumber'], $user_week['year']);
                }
            }

            // add current position to values
            $ranks[] = $this->getCurrentRank($username);

            // return highest rank - minimal value
            return min($ranks);
        }else{

            // user has received no love so far
            return '-';
        }
    }

    // returns an array with week numbers (and years) for given user
    public function getUserWeeks($username){
        $front =Frontend::getInstance();
        $mycompany=$front->getCompany()->getId()||MAIN_COMPANY;

        // get array of week numbers and years in which user has received some love
        // using 'unique_week' to avoid the situation when weeks from different years are grouped as one
        $sql = "SELECT WEEK(love.at) AS weeknumber, YEAR(love.at) AS year, DATE_FORMAT(love.at, '%U%Y') AS unique_week
                        FROM ".LOVE." AS love
                        INNER JOIN ".USERS." AS users ON love.receiver = users.username
                        WHERE users.nickname='$username'
                        AND love.company_id = " . (int) $mycompany . "
                        GROUP BY unique_week";
        $res = mysql_query($sql);

        $weeks = array();
        while($row = mysql_fetch_assoc($res)){
            $weeks[] = array('weeknumber' => $row['weeknumber'], 'year' => $row['year']);
        }
        return $weeks;
    }

	#public function deleteLove($id)
	#{
	#	$front = Frontend::getInstance();
	#	$company = $front->getCompany()->getId() || MAIN_COMPANY;
	#	$sql = "DELETE FROM `" . LOVE . "` WHERE `id` = " . (int)$id . ";";
	#	if (mysql_query($sql)) {
	#		return true;
	#	}
	#	return false;
	#}

    public function getTotalLove(){
        $sql = "SELECT COUNT(*) ".
               "FROM " . LOVE;
        $res = mysql_query($sql);
        $row = mysql_fetch_row($res);
        return $row[0];
    }

    public function formatUserLink($full_nickname, $disp_nickname = null){

        if($disp_nickname === null){
            $disp_nickname = $full_nickname;
        }
        $link = '<a href="' . $full_nickname . '" target = "_blank">' . $disp_nickname . '</a>';
        return $link;
    }

    /**
     * Gets number of average love user sent in given period up to now
     * 
     * @param String $username username(email) of user
     * @param Integer $interval number of days until now
     * @return Integer average number of loves 
     */
    public static function getUserAverageSent($username, $interval = 7){

        $front =Frontend::getInstance();
        $mycompany=$front->getCompany()->getId()||MAIN_COMPANY;

        $average = 0;
        $sql = "SELECT COUNT( * ) AS `loves`
                    FROM `".LOVE."` l
                    WHERE l.at >= DATE_SUB(CURRENT_DATE, INTERVAL $interval DAY)
                    AND l.giver = '$username'
                    AND l.company_id = " . (int) $mycompany;

        $res = mysql_query($sql);
        if($res){
            $row = mysql_fetch_assoc($res);
            $loves = $row['loves'];
            $average = round($loves/$interval);
        }

        return $average;
    }

    /**
     * Gets total number of loves user has sent/received
     *
     * @param String $username username(email) of user
     * @param String $direction is user giver or receiver? (Love::GIVER or Love::RECEIVER)
     * @return Integer total number of loves
     */
    public static function getUserTotal($username, $direction = Love::GIVER){

        $front =Frontend::getInstance();
        $mycompany=$front->getCompany()->getId()||MAIN_COMPANY;

        $total = 0;
        $sql = "SELECT COUNT( * ) AS `loves`
                    FROM `".LOVE."` l
                    WHERE l.$direction = '$username'
                    AND l.company_id = " . (int) $mycompany;

        $res = mysql_query($sql);
        if($res){
            $row = mysql_fetch_assoc($res);
            $total = $row['loves'];
        }

        return $total;
    }


    /**
     * Gets number of senders that have sent love to given user (total value)
     * 
     * @param String $username username(email) of user
     * @return Integer number of unique senders 
     */
    public static function getUserUniqueSenders($username){

        $front =Frontend::getInstance();
        $mycompany=$front->getCompany()->getId()||MAIN_COMPANY;

        $givers = 0;
        $sql = "SELECT COUNT(DISTINCT giver) AS `givers`
                    FROM `".LOVE."` l
                    WHERE l.receiver = '$username'
                    AND l.company_id = " . (int) $mycompany;

        $res = mysql_query($sql);
        if($res){
            $row = mysql_fetch_assoc($res);
            $givers = $row['givers'];
        }

        return $givers;
    }
}

<?php
/*
          Take a date in yyyy-mm-dd format and return it to the user
          in a PHP timestamp
          Robin 06/10/1999
*/
function GetTimeStamp($MySqlDate, $i='')
{
        if (empty($MySqlDate)) $MySqlDate = date('Y/m/d');
        $date_array = explode("/",$MySqlDate); // split the array
        
        $var_year = $date_array[0];
        $var_month = $date_array[1];
        $var_day = $date_array[2];
        $var_timestamp=$date_array[2]."-".$date_array[0]."-".$date_array[1];
        //$var_timestamp=$var_month ."/".$var_day ."-".$var_year;
        return($var_timestamp); // return it to the user
}


function get_love_exchange_user_list($username, $company_id, $filters= " ")
{
    $userLoveCountQuery = "SELECT usr.username 
        FROM ".USERS." as usr
        LEFT OUTER JOIN ".LOVE." as lv ON usr.username=lv.giver OR usr.username = lv.receiver
        WHERE usr.company_id=$company_id ";
    $userLoveCountQuery .= $filters;
    $userLoveCountQuery .= " AND (lv.giver = '" . $username . "' OR lv.receiver = '" . $username . "')";
    $userLoveCountQuery .= " GROUP BY usr.id ORDER BY usr.nickname ASC";
	
    $userList = false;
    
    $res = mysql_query($userLoveCountQuery);
    if($res && mysql_num_rows($res) > 0)
    {
        while ($row = mysql_fetch_array($res, MYSQL_ASSOC))
        {
            $userList[] = $row["username"];
        }
    }
    if(!$userList)
    {
    	// We need to show the user at least, even if no love exchanges were done
    	$userList[] = $username;
    }
    return $userList;
}

function get_username_for_nick($nickname, $company_id)
{
	$usernameForNick = false;
	 
    $nicknameQuery = "SELECT usr.username FROM ".USERS." as usr WHERE usr.company_id=" . $company_id . " AND usr.nickname = '" .$nickname . "' ORDER BY usr.nickname ASC";
    $res = mysql_query($nicknameQuery);
     if($res && mysql_num_rows($res) > 0)
     {
        $row = mysql_fetch_array($res, MYSQL_ASSOC);
        if($row)
        {
            $usernameForNick = $row["username"];
        }
    }
    
    return $usernameForNick;
}

function format_array_as_infilter($inputList)
{
	$formattedFilter = false;
    if($inputList)
    {
    	$formattedFilter = join("','", $inputList);
    	$formattedFilter = "('" . $formattedFilter . "')";
    }
	return $formattedFilter;
}
function log_debug_array($title, $object)
{
  echo  "\n" . $title;
  echo "<pre>";
  print_r($object);
  echo "</pre>";
}

function  getRollupColumn($columnName, $daysInRange)
{
    $dateRangeType = 'd';
    $dateRangeQuery = "DATE_FORMAT(" .$columnName . ",'%Y-%m-%d') ";
    if($daysInRange > 31 && $daysInRange <= 180) {
      $dateRangeType = 'w';
      $dateRangeQuery = "yearweek(" .$columnName . ", 3) ";
    } else if($daysInRange > 180 && $daysInRange <= 365) {
      $dateRangeType = 'm';
      $dateRangeQuery = "DATE_FORMAT(" .$columnName . ",'%Y-%m') ";
    } else if($daysInRange > 365 && $daysInRange <= 730) {
      $dateRangeType = 'q';
      $dateRangeQuery = "concat(year(" .$columnName . "),QUARTER(" .$columnName . ")) ";
    } else if($daysInRange > 730) {
      $dateRangeType = 'y';
      $dateRangeQuery = "DATE_FORMAT(" .$columnName . ",'%Y') ";
    }
    return array('rollupRangeType' => $dateRangeType, 'rollupQuery' => $dateRangeQuery);
}

function  getMySQLDate($sourceDate)
{
    if (empty($sourceDate)) $sourceDate = date('Y/m/d');
    $date_array = explode("/",$sourceDate); // split the array

    $targetDate = mktime(0, 0, 0, $date_array[0]  , $date_array[1], $date_array[2]);

    return date('Y-m-d',$targetDate); 
}

/**
 * quarterByDate()
 * 
 * Return numeric representation of a quarter from passed free-form date.
 * 
 * @param mixed $date
 * @return integer
 */
function quarterByDate($date)
{
    return (int)floor(date('m', strtotime($date)) / 3.1) + 1;
}

/** 
* Fills a series with linear data, filling any gaps with null values. 
* The resulting array can directly be used in a chart assuming the labels use same data set.
*
*
*/
function fillAndRollupSeries($strDateFrom, $strDateTo, $arySeries, $fillWithDate, $dateType = 'd', $zeroFill=false) {
	$arySeriesData = array();
	$aryRollupData = array();
	$currentDate = mktime(0,0,0,substr($strDateFrom,5,2),  substr($strDateFrom,8,2), substr($strDateFrom,0,4));
	$toDate = mktime(0,0,0,substr($strDateTo,5,2),  substr($strDateTo,8,2), substr($strDateTo,0,4));
	$xLabels = array();
	$x1Labels = array();
	$x2Labels = array();
	$xFullLabels = array();
	$previousDate = $currentDate;
	while ($currentDate <= $toDate) {
		$x2Label = null;
		$xFullLabel = null;
		if($dateType == 'd') {
			$key = date('Y-m-d', $currentDate);
			$x1Label = date('d',$currentDate);
			$xFullLabel = date('m/d/Y', $currentDate);
			if(date('d',$currentDate) == '01' || sizeof($x1Labels) == 0) {
				$x2Label= date('M',$currentDate) ;
			} 	
			$currentDate = mktime(0,0,0,substr($key,5,2),  substr($key,8,2)+1, substr($key,0,4));
		} else if($dateType == 'w') {
			if(date("D",$currentDate)=="Mon") {
				$weekStart = $currentDate;
			} else {
				$weekStart = strtotime('-1 week mon', $currentDate);
			}
			$weekEnd = strtotime('+0 week sun', $weekStart);
			$key = date('oW', $weekStart);
			if(date('m', $weekStart) == date('m', $weekEnd)) {
				if(date('m',$weekStart) != date('m',$weekStart- 7*24*60*60) && sizeof($x1Labels) == 0) {
					$x1Label=date('M',$weekStart) . " ";
				}
				else {
					$x1Label="";
				}
				if( sizeof($x1Labels) == 0) {
					if(date('d',$currentDate) == date('d',$weekEnd)){
						$x1Label .= date('d',$currentDate); 
					} else {
						$x1Label .= date('d',$currentDate) ."-" . date('d',$weekEnd) ; 
					}
				} else {
					$x1Label .= date('d',$weekStart) ."-" . date('d',$weekEnd) ; 
				}
				if( sizeof($x1Labels) == 0) {
					if(date('d',$currentDate) == date('d',$weekEnd)){
						$xFullLabel = date('M d',$currentDate) ;
					} else {
						$xFullLabel = date('M d',$currentDate) ." - " . date('d, Y',$weekEnd) ;
					}
				} else {
					$xFullLabel = date('M d',$weekStart) ." - " . date('d, Y',$weekEnd) ;
				}				
			} else {
				$x1Label = date('M d',$weekStart) ."-" . date('M d',$weekEnd) ; 
				$xFullLabel = date('M d',$weekStart) ." - " . date('M d, Y',$weekEnd) ; 
			}
			if (date('m',$weekStart) != date('m',$previousDate)) {
				$x2Label = date('M',$weekStart);
			}	
			if(date('W',$currentDate) == '01' || sizeof($x1Labels) == 0) {
				$x2Label = date('M',$weekStart) . " " .date('Y',$currentDate) ;
			}
		
			if( sizeof($x1Labels) == 0) { // Just for first date
				if(date('m',$weekStart) == date('m',$weekEnd) && date('m',$weekStart) == date('m',$weekStart- 7*24*60*60)) {
					$x2Label=date('M',$weekStart);
				} else {
					$x2Label="";
				}
				$x2Label .= " " . date('Y',$currentDate) ;
			}
			
			// Store the current date as previous for identifying group changes
			$previousDate = $currentDate ;
			$currentDate = strtotime('+1 week', $weekStart); 
		} else if($dateType == 'm') {
			$key = date('Y-m', $currentDate);
			$x1Label = date('M',$currentDate);
			if(date('m',$currentDate) == '01' || sizeof($x1Labels) == 0) {
				$x2Label = date('Y',$currentDate) ;
			}
			$xFullLabel = date('M Y',$currentDate); 
			$currentDate = mktime(0,0,0,substr($key,5,2)+1,  1, substr($key,0,4));
		} else if($dateType == 'q') {
			$currentQuarter = quarterByDate(date('Y-m', $currentDate));
			$key = date('Y', $currentDate) . $currentQuarter;
			$x1Label  = date('Y', $currentDate) . ' Q' . $currentQuarter;
			$xFullLabel = $x1Label; 
			$quarterStart = mktime(0,0,0, 1+ ($currentQuarter - 1) * 3,  1, substr($key,0,4));
			$currentDate = strtotime('+3 month', $quarterStart); 
		} else if ($dateType == 'y') {
			$key = date('Y', $currentDate);
			$x1Label  = date('Y',$currentDate);
			$xFullLabel = $x1Label; 
			$currentDate = mktime(0,0,0,1,  1, substr($key,0,4)+1);
		} 
		
		if($fillWithDate) {
			$x1Labels[] = $x1Label;
			$x2Labels[] = $x2Label;
			$xFullLabels[]= $xFullLabel;
		} else if(isset($arySeries[$key])) {
			$arySeriesData[] = $arySeries[$key];
		} else {
			if($zeroFill==true) { 
				$arySeriesData[] = 0;
			} else {
				$arySeriesData[] = null;
			}
		}
	}
	if($fillWithDate) {
		$arySeriesData = array('x1' => $x1Labels, 'x2' => $x2Labels, 'xFull' => $xFullLabels);
	}
	return $arySeriesData;
}

?>
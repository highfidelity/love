<?php
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved.
//  http://www.lovemachineinc.com


ob_start();
include("config.php");
require_once("class.session_handler.php");
include("helper/check_session.php");
include_once("functions.php");
include_once("chart-functions.php");

if (!isset($_SESSION['company_id'])) {
    return;
}

$limit = 5;
$page = isset($_GET["page"]) ? $_GET["page"] : 1; //Get the page number to show 
if($page == "") 
{
	$page=1; //If no page number is set, the default page is 1 
}
if(isset($_GET['from_date'])) {
  $from_date = $_GET['from_date'];
}
if(isset($_GET['to_date'])) {
  $to_date = $_GET['to_date'];
}
$dateRangeFilter = '';
$dateRangeQueryParameters = '';
if(isset($from_date) && isset($to_date))
{
	$mysqlFromDate = mysql_real_escape_string(GetTimeStamp($from_date));
	$mysqlToDate = mysql_real_escape_string(GetTimeStamp($to_date));
	$dateRangeFilter = ($from_date && $to_date) ? " AND DATE(lv.at) BETWEEN '".$mysqlFromDate."' AND '".$mysqlToDate."'" : "";
	$dateRangeQueryParameters="&from_date=".$from_date."&to_date=".$to_date ;
}

/********************************************************************************************/

function getRowCount($queryFrom)
{
	$query = "SELECT count(*) " . $queryFrom;
	$result=mysql_query($query);
	$row = mysql_fetch_row($result);
	return $row[0];
}

$type="";
$nickname = "";

if(isset($_GET['type'])) {
   $type = addslashes($_GET['type']);
}
if(isset($_GET['cat'])) {
   $cat = addslashes($_GET['cat']);
}
if(!$cat)
{
	$cat="sent";
}

if(isset($_GET['nickname'])) {
	$nickname = addslashes($_GET['nickname']);
}

if(! $nickname )
{
	$nickname = $_SESSION['nickname'];
}


if($nickname) {
	// For now the company is expected to be same as that of logged in user.
	$company_id = $_SESSION['company_id'];
	$username = get_username_for_nick($nickname, $company_id);
}

$query = '';
$queryResult = false;

if ( !empty($username) && $username && $type && $type == 'details')
{
	if( $cat == 'sent') {
		$sqlFrom = 	" FROM  ".LOVE." as lv
		    INNER JOIN  " . USERS . " as usr ON (lv.receiver = usr.username)
		    WHERE lv.company_id = ".$company_id.
				  " AND lv.giver = '".$username."'";
		$sqlFrom .= $dateRangeFilter;
		
		$query = "SELECT lv.why, usr.nickname as receiver, lv.private ";
		$query .= $sqlFrom ;
	    $link = "type=details&cat=$cat&nickname=" . urlencode($nickname) . "&" . $dateRangeQueryParameters;
	} else if($cat == 'received') {
		$sqlFrom = " FROM  ".LOVE." as lv
		    INNER JOIN  " . USERS . " as usr ON (lv.giver = usr.username)
		    WHERE lv.company_id = ".$company_id.
				  " AND  lv.receiver = '".$username."'";
		$sqlFrom .= $dateRangeFilter;
				
		$query = "SELECT lv.why, usr.nickname as giver, lv.private, lv.id, lv.favorite ";
		$query .= $sqlFrom ;
		$link = "type=details&cat=$cat&nickname=" . urlencode($nickname) . "&" . $dateRangeQueryParameters;
	}
    if ($username) {
        if ($username != $_SESSION['username']) $sqlFrom .= " AND lv.private = 0";
    }
	$count = getRowCount($sqlFrom);
	
	if($count > 0)
	{ 
		$pageCount = ceil($count/$limit); 
		$query = $query ." LIMIT " . ($page-1) * $limit . ",$limit";
	  	$queryResult = mysql_query($query);
	}
}
/*else {
    echo "Invalid Input Data. Provide valid inputs to get some love";
    die();
}*/
/*********************************** HTML layout begins here  *************************************/
if($type && $type == "userLoveCountsByDate") {
    // Get nickname for the user who's being viewed
    $viewPointNickname = $nickname;
    if(isset($from_date)) {
      $fromDate = getMySQLDate($from_date);
    }
    if(isset($to_date)) {
      $toDate = getMySQLDate($to_date);
    }
    $fromDateTime = mktime(0,0,0,substr($fromDate,5,2),  substr($fromDate,8,2), substr($fromDate,0,4));
    $toDateTime = mktime(0,0,0,substr($toDate,5,2),  substr($toDate,8,2), substr($toDate,0,4));

    $daysInRange = round( abs($toDateTime-$fromDateTime) / 86400, 0 );
    $rollupColumn = getRollupColumn('lv.at', $daysInRange);
    $dateRangeType = $rollupColumn['rollupRangeType'];
    
    $userLoveCountWhere = " WHERE ";

    if($cat =="received")
    {
        $typeColumn .= " lv.giver";
        $userLoveCountWhere .= " lv.receiver = '$username'"; 
    } 
    else
    {
        $typeColumn .= " lv.receiver";
        $userLoveCountWhere .= " lv.giver= '$username'"; 
    }
    $userLoveCountQuery = "SELECT count(lv.id) as loveCount, count(distinct " . $typeColumn . " ) as uniqueSenders, " . $rollupColumn['rollupQuery'] . " as loveDate
        FROM ".LOVE." as lv";

    if ($username) {
        if ($username != $_SESSION['username']) $userLoveCountWhere .= " AND lv.private = 0";
    }
    $userLoveCountWhere .= $dateRangeFilter ;
    $userLoveCountQuery .= $userLoveCountWhere;
    $userLoveCountQuery .= " GROUP BY loveDate ORDER BY lv.at ASC";
    $messages = array();
    $senders = array();

    $res = mysql_query($userLoveCountQuery);
    if($res && mysql_num_rows($res) > 0) {
        while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
            $messages[$row['loveDate']] = $row['loveCount'];
            $senders[$row['loveDate']] = $row['uniqueSenders'];
        }
    }

    $json_data = array('messages' => fillAndRollupSeries($fromDate, $toDate, $messages, false, $dateRangeType), 'senders' => fillAndRollupSeries($fromDate, $toDate, $senders, false, $dateRangeType), 'labels' => fillAndRollupSeries($fromDate, $toDate, null, true, $dateRangeType));
    $json = json_encode($json_data);
    echo $json;
} else if($queryResult && $cat == 'sent')
{
?>
<table cellpadding="0" cellspacing="0" border="0" class="table-history"
	width="100%">
	<tr>
		<td colspan="3" valign="top">
		<table class="table-history" cellpadding="15" cellspacing="0"
			border="0" width="100%" height="100%" style="font-size: 12px">
			<thead>
				<strong>Love Sent
				<tr class="table-hdng">
					<td class="love-from" align="left"><strong>To</strong></td>
					<td class="love-for" align="left"><strong>For</strong></td>
				</tr>
			</thead>

			<tbody>
			<?php
			$oddRow = 1;
			while($row = mysql_fetch_assoc($queryResult))
			{
				if($oddRow == 1) {
					$rowClass = 'row-history-live rowodd';
					$oddRow = 0;
				}else {
					$rowClass = 'row-history-live roweven';
				}
				?>
				<tr class="<?php echo $rowClass; ?>">
					<td class="love-from" align="left"><?=$row['receiver']?></td>
					<td class="love-for" align="left"
						style="height: auto; overflow: visible;"><?=htmlspecialchars($row['why']) . ($row['private'] ? ' (love sent quietly)' : '')?>
					</td>
				</tr>
				<?php } ?>

			    <tr bgcolor="#FFFFFF"> 
			    <td colspan="3" style="text-align:center;">
					<?php
					    $Nav=""; 
					    if($page > 1) { 
					    $Nav .= "<a class=\"page-number\" href=\"".$_SERVER['PHP_SELF']."?$link&page=" . ($page-1) ."\">Prev</a> &nbsp;"; 
					    } 
					    for($i = 1 ; $i <= $pageCount ; $i++) { 
						if($i == $page) { 
						$Nav .= "$i &nbsp;"; 
						}else{ 
						$Nav .= "<a class=\"page-number\" href=\"".$_SERVER['PHP_SELF']."?$link&page=" . $i . "\" >$i</a> &nbsp;"; 
						} 
					    }
					    if($page < $pageCount) { 
					    $Nav .= " &nbsp;<a class=\"page-number\" href=\"".$_SERVER['PHP_SELF']."?$link&page=" . ($page+1) . "\" >Next</a>"; 
					    } 
					    
					    echo   "Pages : &nbsp;".$Nav; 
					?>
			      </td>
			 </tr>
			</tbody>
		</table>
		</td>
	</tr>
</table>

<?php
} else if($queryResult && $cat == 'received')
{
?>

<div id="dialog" title="Why is this a favorite?"><textarea id="reason" rows="5" cols="60"></textarea><br/><input type="submit" id="sendfav" value="Send"/></div>

<table cellpadding="0" cellspacing="0" border="0" class="table-history"
	width="100%">
	<tr>
		<td colspan="3" valign="top">
		<table class="table-history" cellpadding="15" cellspacing="0"
			border="0" width="100%" height="100%" style="font-size: 12px">
			<thead>
				<tr>
					<td colspan="3"><strong>Love Received</strong></td>
				</tr>
				<tr class="table-hdng">
                                        <td class="love-star" align="left"></td>
					<td class="love-from" align="left"><strong>From</strong></td>
					<td class="love-for" align="left"><strong>For</strong></td>
				</tr>
			
			</thead>
			<tbody>


<script>
$(document).ready(function()
{
  var id = null;

  $("div.nofavorite").mouseover(function ()
  {
    if ($(this).hasClass("nofavorite"))
    {
      $(this).addClass("yesfavorite");
    }
  });

  $("div.nofavorite").mouseout(function ()
  {
    if ($(this).hasClass("nofavorite"))
    {
      $(this).removeClass("yesfavorite");
    }
  });

  $("div.nofavorite").click(function (e)
  {
    if ($(this).hasClass("nofavorite") && !$("#dialog").dialog('isOpen'))
    {
      id = $(this);

      $(this).removeClass("nofavorite");
      $(this).addClass("yesfavorite");

      var x = e.pageX - this.offsetLeft;
      var y = e.pageY - this.offsetTop - $(window).scrollTop();

      if (y + 200 > $(window).height())
      {
        y -= 200 - ($(window).height() - y);
      }

      $("#reason").val("");
      $("#dialog").dialog(
      {
//        modal: true,
        bgiframe: true,
        width: 550,
        height: 190,
        position: [x,y],
//        show: 'fade',
//        hide: 'fade',

        close: function(event, ui)
        {
          id.addClass("nofavorite");
          id.removeClass("yesfavorite");
          $(this).dialog("destroy");
        }
      });
    }
  });

  $("#sendfav").click(function ()
  {
    $("#dialog").dialog("destroy");

    $.ajax({
      type: "GET",
      url: "mylove-favorite.php",
      data:
      {
        "id" : id.attr("id"),
        "reason" : $("#reason").val()
      },
      dataType: "json",
      success: function(result)
      {
        if (!result)
        {
          id.addClass("nofavorite");
          id.removeClass("yesfavorite");
        }
      }
    });
  });
});
</script>


			<?php
			$oddRow = 1;
			while($row = mysql_fetch_assoc($queryResult))
			{
				if($oddRow == 1) {
					$rowClass = 'row-history-live rowodd';
					$oddRow = 0;
				}else {
					$rowClass = 'row-history-live roweven';
				}
				?>
				<tr class="<?=$rowClass?>">
					<td class="love-star"><div class="<?=$row['favorite']?>favorite" id="<?=$row['id']?>"></div></td>
					<td class="love-from" align="left"><?=$row['giver']?></td>
					<td class="love-for" align="left"
						style="height: auto; overflow: visible;"><?=htmlspecialchars($row['why']) . ($row['private'] ? ' (love sent quietly)' : '')?>
					</td>
				</tr>
				<? } ?>
	<tr bgcolor="#FFFFFF"> 
			    <td colspan="3" style="text-align:center;">
					<?php
					    $Nav=""; 
					    if($page > 1) { 
					    $Nav .= "<a class=\"page-number\" href=\"".$_SERVER['PHP_SELF']."?$link&page=" . ($page-1) ."\">Prev</a> &nbsp;"; 
					    } 
					    
					    for($i = 1 ; $i <= $pageCount ; $i++) { 
						if($i == $page) { 
						$Nav .= "$i &nbsp;"; 
						}else{ 
						$Nav .= "<a class=\"page-number\" href=\"".$_SERVER['PHP_SELF']."?$link&page=" . $i . "\" >$i</a> &nbsp;"; 
						} 
					    }
					    
					    if($page < $pageCount) { 
					    $Nav .= " &nbsp;<a class=\"page-number\" href=\"".$_SERVER['PHP_SELF']."?$link&page=" . ($page+1) . "\" >Next</a>"; 
					    } 
					    
					    echo   "Pages : &nbsp;".$Nav; 
					?>
			      </td>
			 </tr>
			</tbody>
		</table>
		</td>
	</tr>
</table>
<?php
} else {
      echo "There is no love you can view at this time. Send some Love!";
}?>

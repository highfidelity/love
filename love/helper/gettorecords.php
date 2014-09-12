<?php ob_start(); 
  //  Copyright (c) 2009, LoveMachine Inc.  
  //  All Rights Reserved.  
  //  http://www.lovemachineinc.com

include_once("../class/frontend.class.php");
$front = Frontend::getInstance();
include_once("../db_connect.php");
include_once("../autoload.php");
if(!$front->isUserLoggedIn()){
    die("User not logged !");
}

$username = $front->getUser()->getUsername();
$company_id = $front->getUser()->getCompany_id();

// generates the pager for love list table
function getListPager($page,$pageMax,$urlParam)
{
    $output = '<div class="pagerDiv"><ul class="pager">';
    $iPage=0;
        $iPage = max(0, $page - 5 );
    if ($iPage + 5 > $pageMax) {
        $iPage = max(0, $pageMax - 5 );
    }
    $iPageStart = $iPage;
    if($page != 1){
        $output .= '<li class="page-number" href="' . $_SERVER['PHP_SELF'] . '?page=1' . $urlParam . '">&lt;&lt;</li>';
        $output .= '<li class="page-number" href="' . $_SERVER['PHP_SELF'] . '?page=' . ($page-1) . $urlParam . '">&lt;</li>';
    }
    while ($iPage < $pageMax) {
        if ($iPage > $iPageStart + 9 && $iPage+1 != $pageMax) {
            $output .= '<li class="morePage">...</li>';
            break;
        }
        if($page == $iPage+1){
            $output .= '<li class="page current">' . ($iPage+1) . '</li>';
        } else {
            $output .= '<li class="page-number" href="' . $_SERVER['PHP_SELF'] . '?page=' . ($iPage+1) . $urlParam . '">' . ($iPage+1) . '</li>';
        }
        $iPage++;
    }
    if($page != $pageMax){
        $output .= '<li class="page-number" href="' . $_SERVER['PHP_SELF'] . '?page=' . ($page+1) . $urlParam . '">&gt;</li>';
        $output .= '<li class="page-number" href="' . $_SERVER['PHP_SELF'] . '?page=' . $pageMax . $urlParam . '">&gt;&gt;</li>';
    }
    $output .= "</ul></div>";
    return $output;
}


  /************************************** Start Pagination ************************************/
$id='';
$Limit = 14;
$page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : 1; //Get the page number to show, set default to 1
  /********************************************************************************************/
$urlParam="";
$query_no_private= "";

if (!empty($_REQUEST["to"])) {
    $urlParam = "&to=" . urlencode($_REQUEST["to"]);
    $parameters = explode(",", $_REQUEST["to"]);
    for ($i = 0; $i < count($parameters); $i++) {
        $parameters[$i] = mysql_real_escape_string($parameters[$i]);
    }

    $query="SELECT  `".LOVE."`.`id`, `".LOVE."`.`giver`, `".LOVE."`.`receiver`, `".LOVE."`.`why`, `".LOVE."`.`private`, `".LOVE."`.`favorite`, `".
        LOVE."`.`favorite_why`, `".USERS."`.`nickname` AS `giver_nickname` FROM `".LOVE."`, `".USERS."` ".
        "WHERE receiver = '".$parameters[0]."' AND `at` BETWEEN '".date("Y-m-d", $parameters[1])."' AND '".
        date("Y-m-d", $parameters[2])."' + INTERVAL 1 DAY AND `".LOVE."`.`company_id` = ".$_SESSION['company_id'].
        " AND private = 0 AND `giver` = `username`";												
} else if (!empty($_REQUEST["tag"])) {
    $urlParam = "&tag=" . urlencode($_REQUEST["tag"]);
    $filterUsername = " ";
    //This mess is to support tag words with ?, ` or " characters
    //The search string for pregex says "use Negative lookback, find a question mark that is not escaped with a preceeding \ "
    // found? becomes found\\? to be able to match found\? stored in database
    $tagFilter =  preg_replace("/(?<!\\\\)\?/","\\\\\\?",addslashes(addslashes(mysql_real_escape_string($_REQUEST['tag']))));

    // $username is used (and not $_REQUEST["username"] ) to be sure that it's the username from login
    $usernameWhere = " AND ( lv.giver = '".mysql_real_escape_string($username)."' or lv.receiver = '".
                    mysql_real_escape_string($username) . "') ";
    if (!empty($_REQUEST["username"])) {
        // the tag is coming from "My Loves", so we need to filter using username
        $urlParam .= "&username=" . urlencode($_REQUEST["username"]);    
        $filterUsername = $usernameWhere;
    }

    // RLIKE is Regex LIKE in mysql 5+. This has been rewritten using a special character class [[:<:]] [[:>:]] that means word boundary (anthing not alpha, numeric, or underscore)
    $query  = "SELECT distinct lv.id, lv.why, lv.private, giver.nickname as 'giver_nickname', receiver.nickname as 'receiver_nickname' FROM ".LOVE." as lv ";
    $query .= " INNER JOIN ".USERS." as giver ON lv.giver = giver.username " ;
    $query .= " INNER JOIN ".USERS." as receiver ON lv.receiver = receiver.username " ;
    $query .= " WHERE  lv.company_id='". $company_id ."' " . $filterUsername;
    $query .= " AND ( lv.why RLIKE  '[[:<:]]" .$tagFilter . "[[:>:]]' )  ";
    $query_no_private = $query;
    $query .= "  AND (private = 0 OR (private = 1 " . $usernameWhere . "))";
}

$sqlRs  = mysql_query($query);
$count  = mysql_num_rows($sqlRs);
$NumberOfPages=ceil($count/$Limit); 
$query1 = $query ." LIMIT " . ($page-1)*$Limit . ",$Limit";
$rt = mysql_query($query1) or error_log("get2rec.mysql_error:.".mysql_error());

if($count > 0){ 
    echo getListPager($page,$NumberOfPages,$urlParam);
?>

		<table width="100%"  border="0" cellpadding="5" cellspacing="1" bgcolor="#88809A" style="text-align: left;">
                  <col width="4%" align="left" />
		  <col width="23%" align="left" />
		  <col width="23%" align="left" />
		  <col width="50%" align="left" />
		   <tr class="table-hdng">
                      <td></td>
		      <td>From</td>
		      <td>To</td>
		      <td>For</td>
				</tr>
				<?php 
				$i = 0;
				while($row = mysql_fetch_array($rt)){
					$i++;
					$why = stripslashes($row['why']);
					if (isset($row['favorite_why'])) {
						$favourite = $row['favorite_why'];
					} else {
						$favourite = '';
					}
					
						?> 
						<tr style="height: 30px;" <?php if($i%2){ ?>class="rowodd" <?php }else{ ?>class="roweven"<?php } ?>>
                              <td><div class="<?=$favourite?>favorite" id="<?=$row['id']?>" onmouseover="over(this, '<?= $favourite ?>')"></div></td>
							<?php if (!empty($_REQUEST["to"])) { ?>
							  <td><?php echo $row['giver_nickname']; ?></td>
							  <td><?php echo stripslashes($parameters[3]); ?></td>
							<?php } else { ?>
							  <td><?php echo $row['giver_nickname']; ?></td>
							  <td><?php echo $row['receiver_nickname']; ?></td>
							<?php } ?>

							<td><?php
                                    $quietly = $row['private'] == 1 ? " (love sent quietly)" : "";
                                    echo htmlspecialchars($why) . $quietly; 
                                ?></td>
						</tr>
				<?php } ?> 
				<tr  style="height: 30px;" bgcolor="#FFFFFF"> 
					<td colspan="4" style="text-align:center;">
					<?php
                    echo getListPager($page,$NumberOfPages,$urlParam);
                ?>
                </td>
		</tr>
		</table>
			
<?php 
    }else{
        $query_no_private .= "  AND private = 1 ";
        $sqlRs  = mysql_query($query_no_private);
        $countP  = mysql_num_rows($sqlRs);

        echo "Oops, this comes from a private <span title='Number of private Loves using this word: " . $countP . "'>love</span>.";
    }
?>

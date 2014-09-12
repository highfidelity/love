<?php 
include("class/frontend.class.php");
$front = Frontend::getInstance();
#include("class/Utils.class.php");
#include_once("db_connect.php");
#include_once("autoload.php");
#include("review.php");

if(!$front->isUserLoggedIn()){
    $front->getUser()->askUserToAuthenticate();
}


?>
<div class="lb">
	<center><?php echo $_GET['username'] ?>'s Stats</center>
	<div class="left">
			<div id="userImage100">
		    <img src="<?php echo SERVER_URL; ?>thumb.php?t=lBO&src=<?php echo Utils::getUserImageByUsername($_GET['username']); ?>&w=100&h=100&zc=0" width="100" height="100" alt="profile" />
      </div>
	</div>
	<div class="right">
		<div class="ranking">
			<img src="img/tooltip/<?php echo $front->getLove()->getTrend($_GET['username']) ?>" title="Last week's position"  width="20px"/> <span class="chartlabel"><?php echo $front->getLove()->getWeekRank($_GET['username']) ?></span><br>
			<img src="img/tooltip/weekson.png" title="Weeks on chart" width="20px"/> <span class="chartlabel"><?php echo $front->getLove()->getWeeksOnChart($_GET['username']) ?></span><br>
			<img src="img/tooltip/peak.png" title="Top Position" width="20px"/> <span class="chartlabel"><?php echo $front->getLove()->getTopPosition($_GET['username']) ?></span><br>
		</div>
	</div>
    
</div>

<!-- <img class="minification" src="images/minimize.png" alt="minimize"/> -->
<div id="mainActionArea">
    <div class="love">
        <div class="mainActionLeft">
        <?php include("view/tofor/love/mainAction.php"); ?>
        </div>
        <div class="actionStats">
        <?php include("view/tofor/love/actionStats.php"); ?>
        </div>
    </div>
     <div class="self_review">
         <div class="mainActionLeft">
         <?php include("view/tofor/review/mainAction.php"); ?>
         </div>
         <div class="actionStats">
         <?php include("view/tofor/review/actionStats.php"); ?>
         </div>
     </div>
     <div class="peer_review">
         <div class="mainActionLeft">
             <?php include("view/tofor/review/mainAction.php"); ?>
        </div>
        <div class="actionStats">
            <?php include("view/tofor/review/peerActionStats.php"); ?>
        </div>
    </div>
</div>
<div id="lowerInfo">
    <div id="companyUserSwitch">
        <a href="#companyTab" id="companyLink" class="currentLink">company <br /><span class="tabText">love</span></a>
        <a href="#userTab" id="userLink" >my <br /><span class="tabText">love</span></a>
    </div>
    <div id="lowerInfoHolder">
        <div class="love">
            <?php include("view/tofor/love/lowerInfo.php"); ?>
         </div>
        <div class="self_review">
            <?php include("view/tofor/review/lowerInfo.php"); ?>
        </div>
        <div class="peer_review">
            <?php include("view/tofor/review/lowerInfoPeerReview.php"); ?>   
        </div>
    </div>
</div>
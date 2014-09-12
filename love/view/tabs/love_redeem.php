 <div id="loveReviewTabs">
        <ul>
            <li><a href="#loveTabContent" title="tittle"><span>Love</span></a></li>
       <?php if(ENABLE_REDEEM_RECOGNITION==true) { // case insensitive just in case :) ?>
            <li><a href="#redeemTabContent" title="tittle"><span>Recognition Periods</span></a></li>            
       <?php } ?>
        </ul>
        <div id="loveTabContent">
        <?php include("view/love/tabContents.php"); ?>
        <?php include("view/tofor/footer.php"); ?>
        </div>
	<?php if(ENABLE_REDEEM_RECOGNITION==true) { // case insensitive just in case :) ?>
        <div id="redeemTabContent">
        <?php include("view/redeem/tabContents.php"); ?>
        <?php include("view/tofor/footer.php"); ?>
        </div>
    <?php } ?>
    </div>

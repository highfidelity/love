<?php

include_once("db_connect.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>LoveMachine</title>
    
    <link rel="stylesheet" href="css/jquery-ui.css" type="text/css" media="all" />
    <link rel="stylesheet" type="text/css" href="css/smoothness/lm.ui.css"/>
    <link rel="stylesheet" type="text/css" href="css/redeemRequests.css"/>
    
    <script type="text/javascript" src="js/jquery-1.4.2.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.8.4.min.js"></script>

    <link rel="stylesheet" type="text/css" media="screen" href="css/ui.jqgrid.css" />

    <script type="text/javascript" src="js/i18n/grid.locale-en.js"></script>
    <script type="text/javascript" src="js/jquery.jqGrid.min.js"></script>
    <script type="text/javascript" src="js/redeemRequests.js"> </script> 
    <script type="text/javascript" src="js/jquery.blockUI.js"></script>
    
 
 </head> 

<body>   
    <div id="content" style="clear:both;">
        <div id='periods_redeem'>
            <div class='topArea'>
                <div class='leftArea'>
                <b>Redeem requests of all active instances sent to LM</b><br/>
                On the right side you have the list of the active instances.<br/>
                Just below you have the list of redeem requests.<br/>
                Bottom of the screen, the Amazon gift card order will be prefilled by the list of cards you want to send.
                </div>
                <div class='rightArea'>
                    <table class="listInstances"></table> 
                    <div id="pagerInstances"></div> 
                    <div style='clear:both;'>
                    </div>
                </div>
                <div style='clear:both;'>
                    <div>
                        <table class="listPeriods"></table> 
                        <div id="pagerPeriods"></div> 
                        <div style='clear:both;'>
                        
                        </div>
                    </div>
                    <input type='button' value='Export selected spans' class='giftPopup'/>
                    <input type='button' value='Put selected spans in Amazon Gift Card Order' class='amazonGiftPopup' 
                            onclick="alert('Sales Chrome extension is not installed !');"
                    />
                    <input type='button' value='hidden for amazon' id='amazonGiftPopup2' style="display:none;" />
                    <span id='info_table_update'></span>
                </div>
            </div>
            <iframe id="amazonIFrame"  style='width:100%;height:500px;'>
            </iframe>
            <div id='dialogForAmazon'  style="display:none;">
            
                <div class='ButtonArea'>
                    <div>
                        1- Verify the amount in Amazon Card ! It should be: $ <span class='total_amount_verif'></span> .<br/>
                        2- Click "Proceed to Checkout" in Amazon form. <br/>
                        3- Continue until payment in Amazon. 
                    </div>
                    <input type='button' class='cancelAmazon' value='Cancel' />
                    <input type='button' class='validAmazon' value='Confirm Amazon Payment Done' />
                </div>
            </div>
        </div>
    </div>
</body>
</html>

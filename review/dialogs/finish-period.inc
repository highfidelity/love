<?php
    $totalGivers = Rewarder::getStatsGivers();
    $totalPoints = Rewarder::getStatsGranted();
    $totalDistributions = Rewarder::getStatsDistributions();
    $totalReceivers = Rewarder::getStatsReceivers();
?>
<div id = "finish-period-popup" title = "End Review Period" style = "display: none;">
    <div id = "form-display">
        <div id = "desc">
    This action will declare the current rewarder period finished,
    and email all recipients their points and how many people gave them to them.
        </div><br />
        <div id = "finish-stats">
        <span class = "emp"><?php echo $totalGivers; ?></span> people have given out total of 
        <span class = "emp"><?php echo $totalPoints; ?></span>
        points in <span class = "emp"><?php echo $totalDistributions; ?></span> unique distributions.<br />
        There are <span class = "emp"><?php echo $totalReceivers; ?></span> recipients of points.
        </div>
    </div>

    <div id = "form-display">
        <input type = "checkbox" name = "reset-balances" />
        <span class = "emp">Reset remaining point balances to zero</span><br /><br />
        <input type = "text" name = "conversion-rate" id = "conversion-rate" class = "money" value = "0" />
        <span class = "emp"> $ Dollars/Point (leave zero to not notify)</span><br /><br />
        <span class = "emp">Signature line:</span><br />
        <textarea name = "signature">Love, your LoveMachine</textarea><br />
        <input type = "submit" name = "end-period" value = "End this Period" />
    </div>
</div>

<div id = "throbber-holder">
    <div id = "throbber-message">
    Sending email notifications...
    </div>
    <div id="throbber"></div>
</div>
 

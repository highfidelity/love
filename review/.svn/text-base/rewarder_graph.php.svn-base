<?php
$is_auditor =  !empty($_SESSION['is_auditor']) ? $_SESSION['is_auditor'] : 0;
if ($is_admin) {
    echo '<div id="rewarder-controls">';
    if ($is_admin) {
        echo '<a href = "#" id = "finish-period">Finish current period</a> ';
    }

    echo '<span style="margin-left:20px;"><a href="rewarder-user-admin.php">Grant</a>';
    if ($audit_mode) {
        echo ' | <a href="rewarder.php">Review</a>';
    } else {
        echo ' | <a href="rewarder.php?audit=1">Audit</a>';
    }
    echo '</span></div>';
}
?>
<div id="raftest">
    <div id="canvas"></div>
    <div id="menuPopupRef" style="display:none;">
        <ul>
        <li class="menuTitle"><span id="rname"></span>  
        <span id="YPerc"></span></li>
        <li class="menuItem"><span id="YAmount"></span></li>
        <li class="menuItem"><a class="clicklove" href="#">love</a></li>
        <li class="menuItem"><a class="clickreview" href="#">review</a></li>
        <li class="menuSeparator">&nbsp;</li>
        <li class="menuItem removeUser"><a click="remove" href="#">remove</a></li>
        <li class="menuFooter">&nbsp;</li>
        </ul>
    
 
    </div>    
    <div id="button-container">
        <div id="addr"></div>
        <button id='resetr' class='button'>Reset</button>
        <button id='updatr' class='button'>Sort</button>
        <button id='publishr' class='button'>Publish</button>
        <button id='unpublishr' class='button'>Unpublish</button>
        <div id="pointsUpdated">Okay, so you've changed things, keep going until you're happy, then click the sort button <br />
        </div>
    </div>
    <div class="button-spacer"><br/></div>
    <div class="rewarderGraphHelp">
Dial in the right mix!
<br/>
Who do you think added the most value between <span class="startDate"></span> and <span class="endDate"></span> ? 
<br/>
To get you started we have listed of all the people you sent love to between <span class="startDate"></span> and <span class="endDate"></span>
 ordered by the amount of love you gave them.
You can add more people to this list or remove people already present.  
<br/>
If you think someone added more value, drag their slider up!
<br/><br/>
    </div>    
    <div class="button-spacer"><br/></div>
</div>
<?php if (!$load_module) {
    include('dialogs/user-review-info.php'); 
} ?>

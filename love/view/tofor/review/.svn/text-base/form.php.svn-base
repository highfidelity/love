<?php

$period_id = isset($_REQUEST['period_id']) ? intval($_REQUEST['period_id']) : 0;

if(empty($period_id)){
    die ("Nothing to do");
}

// get the period info
$period = $review->getPeriodById($period_id);

?>

<div id="review-form">
    <div id="form-content">
        <form method="get">

            <div id="loves">   
                <div class="review_period_header">
                    <div class="review_period_title">
                        <h1><?php 
                                global $front;
                                $nick = $front->getUser()->getnickname();
                                $possesor = (substr($nick, -1, 1) == 's') ? "'" : "'s";
                                echo $nick,$possesor;
                            ?> Review: <?php echo $period['title']; ?> <span id="review-status-message" style = "display: none;"> (Completed!)</span></h1>
                    </div>
                    <div class="review_user_stat">
                        <?php include("view/tofor/review/userStats.php"); ?>
                    </div>
                </div>
                <ul id="love-list"></ul>
                <div class="add-love" id="add-love"><a href="#">Add love</a></div>
            </div>
            <div class="buttons" id="review_wizard">
                <input type="submit" value="Back" id="back" name="back" />
                <input type="submit" value="Next" class="next" id="next" name="next" />
            </div>
        </form>
        <div id="complete-text" class = "center-text review-message">Review is complete!</div>
        <div id="closed-text" class = "center-text review-message">Review period is closed.</div>
        <div id="not-started-text" class = "center-text review-message">Review period has not started yet.</div>
    </div>
<div>

<?php
    include_once('wizard_strings.php');
    include_once('send_email.php');

    $position = $periods->getCurrentPeriodCount();
    $period = $periods->getPeriodByPosition($position);
    $date_format = 'j F Y';

    $replacements = array(
                        'nickname' => $_SESSION['nickname'],
                        'love_limit' => '<span class="love-limit"></span>',
                        'period_start' => date($date_format, strtotime($period['start_date'])),
                        'period_end' => date($date_format, strtotime($period['end_date'])),
                         );
    $current_wiz_strings = templateReplace($wizard_strings, $replacements);
    foreach($current_wiz_strings as &$to_br){
        $to_br = nl2br($to_br);
    }
?>
<script type="text/javascript">
    var wizard_strings = <?php echo json_encode($current_wiz_strings); ?>;
</script>

    <div class="reviewPeriodTitle" >
        Review Period
    </div>
    <p class="reviewPeriodMessage"></p>
    <div class="selector-holder">
        <div>
            <div class="carousel-holder">
                <div class="carousel">
                    <div style="margin-left:auto;margin-right:auto;">
                    <div class="prev-period scroll" title="Previous review period">
                        <div class="iconsListElement" >
                        </div>
                    </div>
                    <div class="period-title"></div>
                     <div class="next-period scroll" title="Next review period">
                        <div class="iconsListElement" >
                        </div>
                    </div>
                    </div>
               </div>
                <div class="closing-date"></div>
            </div>
        </div>
    </div>
    <div id="wizard-floater" style="display: none; height: auto; width: auto; background: #F0F0F0;" class="ui-dialog ui-widget ui-widget-content ui-corner-all  ui-draggable ui-resizable" tabindex="-1" role="dialog" aria-labelledby="ui-dialog-title-user-info"><div id="user-info" class="ui-dialog-content ui-widget-content" style="width: auto; min-height: 0px; height: auto;"><div id = "wizard-floater-content">
            <div id="wizard-text"></div>
        </div>
        <input type = "submit" value = "Next" class = "next" style = "display: none;" />
        <input type = "submit" value = "Continue" id = "hide-floater" /></div><div class="ui-resizable-handle ui-resizable-n" unselectable="on" style="-moz-user-select: none;"></div><div class="ui-resizable-handle ui-resizable-e" unselectable="on" style="-moz-user-select: none;"></div><div class="ui-resizable-handle ui-resizable-s" unselectable="on" style="-moz-user-select: none;"></div><div class="ui-resizable-handle ui-resizable-w" unselectable="on" style="-moz-user-select: none;"></div><div class="ui-resizable-handle ui-resizable-sw" style="z-index: 1002; -moz-user-select: none;" unselectable="on"></div><div class="ui-resizable-handle ui-resizable-ne" style="z-index: 1003; -moz-user-select: none;" unselectable="on"></div><div class="ui-resizable-handle ui-resizable-nw" style="z-index: 1004; -moz-user-select: none;" unselectable="on"></div></div>

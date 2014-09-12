<?php
    $review->setUserEmail($user->getUsername());
    $user_love = $review->getPeriodLoves($period_id);
?>

<div id = "love-selector" title = "Select loves" style = "display: none;">
<div id = "love-left"></div>
<table>
    <thead class = "table-hdng" >
        <tr>
        <td></td><td class = "headFrom">From</td><td class = "headFor">For</td><td class = "headWhen">Date</td>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach($user_love as $love){
                $checked = !empty($love['review_love_id']) ? 'checked = "checked"' : '';
        ?>
        <tr>
            <td>
                <input type = "checkbox" class = "love-check" <?php echo $checked; ?> />
                <input type = "hidden" class = "love_id" value = "<?php echo $love['id']?>">
                <input type = "hidden" class = "review_love_id" value = "<?php echo $love['review_love_id']?>">
                </td>
            <td class = "headFrom"><?php echo $love['nickname']; ?></td>
            <td class = "headFor"><?php echo $love['why']; ?></td>
            <td class = "headWhen"><?php echo Utils::relativeTime($love['delta']); ?></td>
        </tr>
        <?php
            }
        ?>
    </tbody>
</table>
</div>
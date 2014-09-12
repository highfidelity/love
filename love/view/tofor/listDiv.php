<?php 
    if (!isset($justUser)) $justUser = false;
    if (!isset($current_username)) $current_username = $front->getUser()->getUsername();
?>
<div class="listDiv">
    <table class="table-history">
        <thead>
            <tr class="table-hdng">
                <td class="headFrom">From</td>
                <td class="headTo">To</td>
                <td class="headFor">For</td>
                <td class="headWhen"><a href='javascript:;'>When</a></td>
                <?php #if ((!$justUser) && ($front->getUser()->getCompany_admin())) : ?>
                    <!--<td class="headDelete">Delete</td>-->
                <?php #endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php echo $front->getLove()->getLoveList($current_username, 1, $justUser, true);?>
        </tbody>
  </table>
  <?php #if ((!$justUser) && ($front->getUser()->getCompany_admin())) : ?>
        <!--<div class="deleteButton"><input type="button" value="Delete Selected" /></div>-->
  <?php #endif; ?>
  <div style="clear:both;"></div>
  <div class="pagerDiv"><?php echo $front->getLove()->getListPager(1); ?></div>
</div>

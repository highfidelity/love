  <h1 class="mostLoved">Most Loved</h1>
  <h2>In Past 7 Days</h2>
  <ul id="topLove">
  <?php echo $front->getLove()->getMostLoved();?>
  </ul>
  <p id="loveNotification" class="mostLoved"><?php echo $front->getLoveNotification(); ?></p>

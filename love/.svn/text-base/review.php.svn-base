<?php
if ($_SERVER['PHP_SELF']==__FILE__) die("Invalid request"); 
require_once('class/LoveUser.class.php');
require_once('class/Periods.class.php');
require_once('class/Review.class.php');

$user = new LoveUser();
$periods = new Periods($user->getId());
$review = new Review($user->getId(),$periods);
$review->setUserEmail($user->getUsername());


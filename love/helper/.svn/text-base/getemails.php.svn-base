<?php 

//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com

ob_start();

include("../class/frontend.class.php");
$front = new Frontend();

if(!$front->isUserLoggedIn()){
     $pageURL = 'http';
     if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
     $pageURL .= "://";
     if ($_SERVER["SERVER_PORT"] != "80") {
         $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
     } else {
         $pageURL .= $_SERVER["SERVER_NAME"];
     }
     die();
}
// supports outputting user
if (isset($_GET['user']) && isset($_GET['var'])) {
    echo "{$_GET['var']}.user = '" . $front->getUser()->getNickname() . "';\n" ;
    // support for just returning the username / logged in status for bookmarklet
    if (array_key_exists('userOnly', $_GET)) {
        exit;
    }
}

// supports outputting as variable assignment
if (isset($_GET['var']) && $_GET['var'] != '') {
    echo "{$_GET['var']}.emails = ";
}
// supports getting all records
if (isset($_GET['term']) && $_GET['term'] != '') {
  $q = mysql_real_escape_string(strtolower($_GET['term']));
} else {
  $q = '';
}

$isemail = (strpos($q, '@') !== false);
$data = array();

if (strlen($q) < 1) {
  $q = '';
} else {
  if (isset($_GET['limit']) && intval($_GET['limit']) > 0) {
    $limit = intval($_GET['limit']);
  } else {
    $limit = 1500;
  }

  $query = "select distinct(giver),receiver from ".LOVE." where ".
           "( lower(giver) like '$q%' and receiver= '".$front->getUser()->getUsername()."' ) or ".
           "( lower(receiver) like '$q%' and giver= '".$front->getUser()->getUsername()."' ) ".
           "order by giver asc limit ".$limit;
  $result = mysql_query($query);

  while ($result && $row=mysql_fetch_assoc($result)) {
    if ($row['giver'] != $front->getUser()->getUsername()) {
        $data[$row['giver']] = $row['giver'];
    } else {
        $data[$row['receiver']] = $row['receiver'];
    }
  }
}

$cid = $front->getUser()->getCompany_id();

if ($q == '') {

  $cid = $front->getUser()->getCompany_id();
  $query = "SELECT username, nickname, id FROM ".USERS." WHERE company_id='".$front->getUser()->getCompany_id()."' ";
  $query .= "AND (username <> '" . $front->getUser()->getUsername() . "') AND `active` = 1 AND `removed` = 0 ";
  $query .= " ORDER BY username ASC";

  $result = mysql_query($query);
  $returnID = "";
  while ($result && $row=mysql_fetch_assoc($result)) {
    $data[$row['username']] = $row['nickname']." (". $row['username'] . ") --id:" . $row['id'] ;
  }

} else {

  if (!empty($cid)) {
    $query = "select username, nickname, id from ".USERS." where company_id='".$front->getUser()->getCompany_id()."' AND `removed` = 0";
    if ($isemail) {
        $query .= "and username like '$q%' ) ";
    } else {
        $query .= "and (username like '$q%' or nickname like '$q%') and (username <> '" . $front->getUser()->getUsername() . "' and nickname <> '" . $front->getUser()->getNickname() . "') AND `removed` = 0";
    }
    $query .= "order by username asc limit ".$limit;
    $result = mysql_query($query);
    $returnID = "";
    while ($result && $row=mysql_fetch_assoc($result)) {
      $data[$row['username']] = $row['nickname']." (". $row['username'] . ") --id:" . $row['id'] ;
    }
  }

}

  // set a default maximum for when all emails are requested
  if ($limit = -1) { $limit = 1500; }
  sort($data);
  echo '[';
  for ($i = 0; $i < $limit && $i < count($data); $i++) {
    if ( $i != 0 ) {
        echo ",";
    }
    $idPos = strpos($data[$i]," --id:");
    if ( $idPos != false ) {
        $idL = substr($data[$i],$idPos+6);
        $label = substr($data[$i],0,$idPos);
    } else {
        $idL = "-1";
        $label = $data[$i];
    }
    echo '{"id":"' . $idL . '","label":"' . $label .'"}';
  }
  echo ']';

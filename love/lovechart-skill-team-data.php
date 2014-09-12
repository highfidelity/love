<?php
//  Copyright (c) 2009, LoveMachine Inc.                                                                                                                          
//  All Rights Reserved.                                                                                                                                          
//  http://www.lovemachineinc.com

ob_start(); 
include("config.php");
require_once("class.session_handler.php");
include("helper/check_session.php");
// Database Connection Establishment String
mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD);
// Database Selection String
mysql_select_db(DB_NAME);
include_once("functions.php");

if (!isset($_SESSION['company_id'])) {
    return;
}

function GetTimeStamp($MySqlDate, $i='')
{
        /*
                Take a date in yyyy-mm-dd format and return it to the user
                in a PHP timestamp
                Robin 06/10/1999
        */
        if (empty($MySqlDate)) $MySqlDate = date('Y/m/d');
        $date_array = explode("/",$MySqlDate); // split the array
        
        $var_year = $date_array[0];
        $var_month = $date_array[1];
        $var_day = $date_array[2];
        $var_timestamp=$date_array[2]."-".$date_array[0]."-".$date_array[1];
        //$var_timestamp=$var_month ."/".$var_day ."-".$var_year;
        return($var_timestamp); // return it to the user
}


function EnumArg($name, $values) {
    return isset($_GET[$name]) && array_search($_GET[$name], $values) !== false ? $_GET[$name] : null;
}

function DateArg($name) {
    return isset($_GET[$name]) ? GetTimeStamp($_GET[$name]) : null;
}

$subject = EnumArg('subject', array('skill', 'team', 'love_within_team', 'love_between_teams', 'most_active_team_members'));
$from_date = DateArg('from_date');
$to_date = DateArg('to_date');

if (!$subject || !$from_date || !$to_date) {
    exit;
}

$res = mysql_query("SELECT usr.company_id FROM ".USERS." as usr LEFT JOIN ".COMPANY." as c on c.id=usr.company_id WHERE usr.username ='".$_SESSION['username']."'");
if(!$res) {
    exit;
} else {
    if (!mysql_num_rows($res)) {
        mysql_free_result($res);
        exit;
    }

    $row = mysql_fetch_row($res);
    $company_id = $row[0];
    mysql_free_result($res);
}

function fetch_rows($sql) {
    $res = mysql_query($sql);
    
    if ($res) {
        $result = array();
    
        while ($row = mysql_fetch_row($res)) {
            $result[] = $row;
        }
    
        mysql_free_result($res);
    
        return $result;
    } else {
        return null;
    }
}

function map($rows, $key_name, $value_name) {
    if ($rows === null) {
        return null;
    }

    $result = array();

    foreach ($rows as $row) {
        $result[$row[$key_name]] = $row[$value_name];
    }

    return $result;
}

function teams($company_id) {
    $USERS = USERS;
    $sql = "SELECT team, count(username) AS count FROM $USERS AS usr WHERE usr.company_id = $company_id GROUP BY team";
    return map(fetch_rows($sql), 0, 1);
}

/*function message_counts_by_users($company_id, $team, $type, $from_date, $to_date) {
    $USERS = USERS;

    $team = mysql_real_escape_string($team);
    $cls_join_on = 'usr.username = lv.' . $type;
    $cls_date = "DATE(lv.at) BETWEEN '".mysql_real_escape_string($from_date)."' AND '".mysql_real_escape_string($to_date)."'";
    $sql = <<<END
SELECT
    usr.username AS subject,
    COUNT(*) AS count
FROM
    $USERS AS usr LEFT JOIN $LOVE AS lv
        ON $cls_join_on
WHERE
    usr.company_id = $company_id
    usr.team = '$team'
    AND $cls_date
GROUP BY
    subject
ORDER BY
    count DESC
END;

    $res = mysql_query($sql);
    
    if ($res) {
        $result = array();
    
        while ($row = mysql_fetch_assoc($res)) {
            $result[$row['team']] = $row['count'];
        }
    
        mysql_free_result($res);
    
        return $result;
    } else {
        return null;
    }
}*/

function getData($company_id, $subject, $type, $from_date, $to_date) {
    $cls_join_on = 'usr.username = lv.' . $type;
    $cls_date = "DATE(lv.at) BETWEEN '".mysql_real_escape_string($from_date)."' AND '".mysql_real_escape_string($to_date)."'";

    $USERS = USERS;
    $LOVE = LOVE;

    if ($subject == 'love_within_team') {
        return fetch_rows("
            SELECT
                $type.team AS subject,
                COUNT(*) AS count
            FROM
                ($LOVE AS lv LEFT JOIN $USERS AS giver ON giver.username = lv.giver)
                    LEFT JOIN $USERS AS receiver ON receiver.username = lv.receiver
            WHERE
                giver.company_id = $company_id
                AND receiver.company_id = $company_id
                AND giver.team = receiver.team
                AND $cls_date
            GROUP BY
                subject
            ORDER BY
                count DESC
        ");
    } elseif ($subject == 'love_between_teams') {
        return fetch_rows("
            SELECT
                $type.team AS subject,
                COUNT(*) AS count
            FROM
                ($LOVE AS lv LEFT JOIN $USERS AS giver ON giver.username = lv.giver)
                    LEFT JOIN $USERS AS receiver ON receiver.username = lv.receiver
            WHERE
                giver.company_id = $company_id
                AND receiver.company_id = $company_id
                AND giver.team != receiver.team
                AND $cls_date
            GROUP BY
                subject
            ORDER BY
                count DESC
        ");
    } elseif ($subject == 'most_active_team_members') {
        $result = array();

        foreach (array_keys(teams($company_id)) as $team) {
            $counts = fetch_rows("
                SELECT
                    usr.username AS subject,
                    COUNT(*) AS count
                FROM
                    $USERS AS usr LEFT JOIN $LOVE AS lv
                        ON $cls_join_on
                WHERE
                    usr.company_id = $company_id
                    AND usr.team = '$team'
                    AND $cls_date
                GROUP BY
                    subject
                ORDER BY
                    count ASC
            ");

            $total = 0;

            foreach ($counts as $one) {
                $total += $one[1];
            }

            $active = count($counts);

            if ($total) {
                $sum = 0;
                foreach ($counts as $one) {
                    if (($sum + $one[1]) / $total > 0.2) {
                        break;
                    }

                    $sum += $one[1];
                    $active--;
                }
            }

            $result[] = array($team, $active);
        }

        return $result;
    } else {
        return fetch_rows("
            SELECT
                usr.$subject AS subject,
                COUNT(*) AS count
            FROM
                $USERS AS usr LEFT JOIN $LOVE AS lv
                    ON $cls_join_on
            WHERE
                usr.company_id = $company_id
                AND $cls_date
            GROUP BY
                subject
            ORDER BY
                count DESC
        ");
    }

        /*if ($subject == 'love_within_team') {
            $teams = teams($company_id);
            foreach ($records as $i => $record) {
                $records[$i][1] /= $teams[$record[0]];
            }
        }*/
}

$sent = getData($company_id, $subject, 'giver', $from_date, $to_date);
$received = getData($company_id, $subject, 'receiver', $from_date, $to_date);

if ($sent !== null && $received !== null) {
    echo json_encode(array('sent' => $sent, 'received' => $received));
}
?>

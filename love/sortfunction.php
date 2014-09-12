<?php
//  Copyright (c) 2010, LoveMachine Inc.
//  All Rights Reserved.
//  http://www.lovemachineinc.com

function setSort($sorthead)
{
	$sorthead = strtoupper($sorthead);
	if($sorthead== 'NICKNAME')
		return $sort = 'nickname';
	if($sorthead ==  'EMAIL')
		return $sort = 'username';
	if($sorthead =='ADMIN')
	 return $sort = 'company_admin';
	if($sorthead == 'CONFIRMED BY')
		return $sort = 'company_confirm';
	if($sorthead == 'JOINED')
		return $sort = 'joined';
	else
		return $sort = 'id';
}
function setDir($dir)
{
	if ($dir == 'desc') {
		$dir = strtoupper(
		$dir);
	} else {
		$dir = 'ASC';
	}
	return $dir;
}
?>
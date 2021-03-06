<?php
/***************************************************************************
 *   copyright				: (C) 2008 - 2015 WeBid
 *   site					: http://www.webidsupport.com/
 ***************************************************************************/

/***************************************************************************
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version. Although none of the code may be
 *   sold. If you have been sold this script, get a refund.
 ***************************************************************************/

define('InAdmin', 1);
$current_page = 'users';
include '../common.php';
include $include_path . 'functions_admin.php';
include 'loggedin.inc.php';

unset($ERR);

if (isset($_POST['delete']) && is_array($_POST['delete']))
{
	if (in_array($_SESSION['WEBID_ADMIN_IN'], $_POST['delete']))
	{
		$ERR = $MSG['1100'];
	}
	else
	{
		$delete = '';
		$i = 0;
		foreach ($_POST['delete'] as $id)
		{
			if ($i != 0) $delete .= ', ';
			$delete .= $id;
			$i++;
		}
		$query = "DELETE FROM " . $DBPrefix . "adminusers WHERE id IN (:delete)";
		$params = array();
		$params[] = array(':delete', $delete, 'str');
		$db->query($query, $params);
		$ERR = $MSG['1100'];
	}
}

$STATUS = array(
	1 => '<span style="color:#00AF33"><b>' . $MSG['566'] . '</b></span>',
	2 => '<span style="color:#FF0000"><b>' . $MSG['567'] . '</b></span>'
);

$query = "SELECT * FROM " . $DBPrefix . "adminusers ORDER BY username";
$db->direct_query($query);

$bg = '';
while ($User = $db->fetch())
{
    $created = substr($User['created'], 4, 2) . '/' . substr($User['created'], 6, 2) . '/' . substr($User['created'], 0, 4);
    if ($User['lastlogin'] == 0)
    {
		$lastlogin = $MSG['570'];
    }
    else
    {
		$lastlogin = date('d/m/Y H:i:s', $User['lastlogin'] + $system->tdiff);
    }

    $template->assign_block_vars('users', array(
			'ID' => $User['id'],
			'USERNAME' => $User['username'],
			'STATUS' => $STATUS[$User['status']],
			'CREATED' => $created,
			'LASTLOGIN' => $lastlogin,
			'BG' => $bg
			));
	$bg = ($bg == '') ? 'class="bg"' : '';
}

$template->assign_vars(array(
		'ERROR' => (isset($ERR)) ? $ERR : ''
		));
		
$template->set_filenames(array(
		'body' => 'adminusers.tpl'
		));
$template->display('body');

?>

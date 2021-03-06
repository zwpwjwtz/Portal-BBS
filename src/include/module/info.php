<?php

function getUserInfo($params)
{
	loadUI('information');
	if (!isset($_SESSION['SysID'])) return;
	$usrID = $_SESSION['SysID'];
	require_once(BBS_ROOT.'/include/module/SQL.php');
	$usrInfo = new SQL_User;
	global $usrInfoList;
	$usrInfoList = $usrInfo->getInfOfUser($usrID);
	$usrInfoList['Code']='';	//密码信息不通过global共享
}

function updateUserInfo($params)
{
if (!isset($_SESSION['SysID'])) return;
$usrID = $_SESSION['SysID'];
require_once(BBS_ROOT.'/include/module/SQL.php');
$usrInfo = new SQL_User;
$usrInfoList = $usrInfo->getInfOfUser($usrID);

if (isset($params['INFO_ACTION']))
{
    $info_action = $params['INFO_ACTION'];
    switch($info_action)
    {
    case 'change_all_info':
	$newPW = $params['newPW'];
	$checkPW = $params['checkPW'];
        $newEmail = strip_tags(trim($params['newEmail']));
        if (!preg_match('/^([a-zA-Z0-9_-])+@[a-zA-Z0-9_-]+(\.([a-zA-Z0-9_-]{2,3})){1,}$/iu',$newEmail))
        {
            echo 'Email invalid!<br />';
            break;
        }
	$curPW = sha1($params['curPW']);
	
	if ($params['curPW'] == '')
	{
        setSysMsg('result','请输入原密码！');
		//echo 'Please input original password!<br />';
		break;
	}
	
	if ($curPW != $usrInfoList['Code'])
	{
        setSysMsg('result','原密码错误！');
	    //echo "wrong password<br />";
	    break;
	}

	if ($newPW != $checkPW)
	{
        setSysMsg('result','新密码不一致！');
	    //echo "not consistent<br />";
	    break;
	}

	if ($newPW != null)
	{
		$usrInfo->resetUserCode($usrID, sha1($newPW));
        setSysMsg('result','密码已经更改');
		//echo "password changed<br />";
	}

	if ($newEmail != null && $newEmail!=$usrInfoList['Email'])
	{
		$regex = "/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/";
		if (preg_match($regex, $newEmail))
		{
			$usrInfo->resetUserEmail($usrID, $newEmail);
            setSysMsg('result','邮箱已经更改');
			//echo "email changed<br />";
		}
		$usrInfoList['Email'] = $newEmail;
	}
    }
}
}
?>

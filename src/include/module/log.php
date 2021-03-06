<?php
/**
 * Project: NJUOPEN/Portal-BBS
 * Contributor:WHZ
 * Filename: log.php
 */

//参考:blog.csdn.net/sysprogram/article/details/21107041
function login($params) {
    //print_r($params);
    $username = $params['username'];
    $password = sha1($params['password']);
    //连接数据库
    require_once(BBS_ROOT.'/include/module/SQL.php');
    $user = new SQL_User;
    //认证
    $user_info = $user->getInfOfUserByName($username);
    if ( !empty($user_info) && $user_info['Name'] == $username && $user_info['Code'] == $password) { //登录成功
    $_SESSION['Name']  = $user_info['Name'];
    $_SESSION['SysID'] = $user_info['SysID'];
    //echo 'Login success<br />';
    setSysMsg('result','您已成功登录！');
    // 记录头像路径
    if (file_exists(BBS_ROOT.'/userfile/'.$user_info['Picture']))
    {
        $_SESSION['avatar'] = BBS_WEB_ROOT.'/userfile/'.$user_info['Picture'];
    }
    else
    {
        $_SESSION['avatar'] = BBS_WEB_ROOT.'/userfile/default-avatar';
    }

    //exit;
    } else {
        //cho("Login failed<br />");
        setSysMsg('result','登录失败！');
        setSysMsg('help','请重新登录');
    }
}

/**
 * 登出
 */
function logout() {
    unset($_SESSION['SysID']);
    unset($_SESSION['Name']);
    //echo "Log out success<br />";
    setSysMsg('result','您已成功登出！');
}


function register_show_form() {
	loadUI('register');
}

function register($params) {
	loadUI('general');
    $email = strip_tags(trim($params['email']));
    if (!preg_match('/^([a-zA-Z0-9_-])+@[a-zA-Z0-9_-]+(\.([a-zA-Z0-9_-]{2,3})){1,}$/iu',$email)) return;
    $name = strip_tags(trim($params['username']));
    if (!preg_match('/^(?!_)(?!.*?_$)[a-zA-Z0-9_\x{4e00}-\x{9fa5}]+$/u',$name)) return;
    $pw = $params['password'];
    $repw = $params['repassword'];
    if ($email=='' || $name=='' || $pw=='' || $repw=='') return;
    if ($pw == $repw) {
        require_once(BBS_ROOT."/include/module/SQL.php");
        $pw = sha1($pw);
        $newUser = new SQL_User;
        if($newUser->getInfOfUserByName($name)!=NULL)	//目前限定用户名不能重复
        {
            setSysMsg('result','注册失败，该用户已经存在');
            //echo 'User exists.';
            return;
        }
        $newUser->addUser($name, $pw, 'default-avatar', 0, 0, $email, 0, 18);
        setSysMsg('result','注册成功！请使用新用户名和密码登录');        
        //echo "User Registered.<br />";
    } else {
        setSysMsg('result','注册失败，密码和验证密码不匹配');
        //
        echo "password not match<br/>";
    }
}
?>

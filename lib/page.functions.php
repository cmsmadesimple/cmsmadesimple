<?php

function check_login(&$config) {
	if (!isset($_SESSION["user_id"])) {
		redirect($config->root_url."/admin/login.php");
	}
}

function check_admin(&$config) {

}

function & strip_slashes(&$str)
{
        if(is_array($str))
        {
                while(list($key, $val) = each($str))
                {
                        $str[$key] = strip_slashes($val);
                }
        }
        else
        {
                $str = stripslashes($str);
        }

        return $str;
}

?>

<?php

function check_login() {

	if (!isset($_SESSION["user_id"])) {
		redirect("/admin/login.php");
	}

}

function check_admin() {

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

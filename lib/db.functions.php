<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://cmsmadesimple.sf.net
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#BUT withOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

class DB {

	function DB($config) {
		#if ($type == "sqlite") {
			#$this->db = $config->db_name;
			#$this->link = sqlite_connect($this->db);
		#} else {
			$this->host = $config->db_hostname;
			$this->db = $config->db_name;
			$this->user = $config->db_username;
			$this->pass = $config->db_password;
			$this->link = mysql_connect($this->host, $this->user, $this->pass);
			mysql_select_db($this->db, $this->link);
		#}
	}
	
	function query($query) {
		return mysql_query($query, $this->link);
		#return sqlite_query($query, $this->link);
	}

	function rowcount($result) {
		return mysql_num_rows($result);
		#return sqlite_num_rows($result);
	}

	function rowsaffected() {
		if (mysql_affected_rows($this->link) > 0) {
			return true;
		} else {
			return false;
		}
		#sqlite should return TRUE of FALSE for $result, this should be good enough for how we use it
	}

	function getresulthash($result) {
		return mysql_fetch_array($result, MYSQL_ASSOC);
		#sqlite_fetch(array($result, SQLITE_ASSOC);
	}

	function freeresult($result) {
		mysql_free_result($result);
		#no sqlite equiv -- just ignore it
	}

	function close() {
		mysql_close($this->link);
		#sqlite_close($this->link);
	}

	function escapestring($string) {
		return mysql_escape_string($string);
		#sqlite_escape_string($string);
	}

	function insertid() {
		return mysql_insert_id($this->link);
		#sqlite_insert_id($result);
	}

}

require_once(dirname(dirname(__FILE__)).'/smarty/Smarty.class.php');

class Smarty_DB extends Smarty {

        function Smarty_DB(&$config)
        {
                $this->Smarty();

                $this->configCMS = $config;

                $this->template_dir = $config->root_path.'/schemas/';
                $this->compile_dir = $config->root_path.'/smarty/cms/templates_c/';
                $this->config_dir = $config->root_path.'/smarty/cms/configs/';
                $this->cache_dir = $config->root_path.'/smarty/cms/cache/';
                $this->plugins_dir = $config->root_path.'/smarty/cms/plugins/';

                $this->compile_check = true;
                $this->caching = false;
                $this->assign('app_name','CMS');
                $this->debugging = false;
                $this->force_compile = true;
	}
}

# vim:ts=4 sw=4 noet
?>

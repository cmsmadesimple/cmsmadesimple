<?php

class DB {

	function DB($config) {
		$this->host = $config->db_hostname;
		$this->db = $config->db_name;
		$this->user = $config->db_username;
		$this->pass = $config->db_password;
		$this->link = mysql_connect($this->host, $this->user, $this->pass);
		mysql_select_db($this->db, $this->link);
	}
	
	function query($query) {
		$result = mysql_query($query, $this->link);
		return $result;
	}

	function close() {
		mysql_close($this->link);
	}

}

require_once('Smarty.class.php');

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

?>

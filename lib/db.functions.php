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

?>

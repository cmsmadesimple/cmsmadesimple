<?php

@error_reporting(E_ERROR);

class SimpleSlider extends CMSModule{

	function SimpleSlider(){
		$this->CMSModule();
	}

	function GetName(){
		return 'SimpleSlider';
	}
		
	function GetFriendlyName()
    {
        return 'Manage Simple Slider';
    }
		
	function Install()
	{
		global $gCms;
		//Get a reference to the database
		$db = $this->cms->db;

    	// mysql-specific, but ignored by other database
    	$taboptarray = array('mysql' => 'TYPE=MyISAM');

    	//Make a new "dictionary" (ADODB-speak for a table)
	    $dict = NewDataDictionary($db);

    	//Add the fields as a comma-separated string.
   		$fields = "id I AUTO KEY,
			slide I DEFAULT 0,
			title C(50),
			imagelink C(255),
			description X ";

       	//Note the naming scheme that should be followed when adding tables to the database,
       	// so as to make it easy to recognize who the table belongs to, and to avoid conflict with other modules.
       	$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_simpleslider_images', $fields, $taboptarray);
       	$dict->ExecuteSQLArray($sqlarray);
			
    	//Add the fields as a comma-separated string.
   		$fields = "showid I AUTO KEY,
			showname C(50),
			width I,
			height I,
			fadetime I";

       	//Note the naming scheme that should be followed when adding tables to the database,
       	// so as to make it easy to recognize who the table belongs to, and to avoid conflict with other modules.
       	$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_simpleslider_shows', $fields, $taboptarray);
       	$dict->ExecuteSQLArray($sqlarray);	
		
		$sqlarray = $dict->CreateIndexSQL('showname', cms_db_prefix().'module_simpleslider_shows', 'showname', array('UNIQUE'));	
       	$dict->ExecuteSQLArray($sqlarray);
       			
    	//Add the fields as a comma-separated string.
   		$fields = "showid I,
			imageid I
			";

       	// so as to make it easy to recognize who the table belongs to, and to avoid conflict with other modules.
       	$sqlarray = $dict->CreateTableSQL(cms_db_prefix().'module_simpleslider_showimages', $fields, $taboptarray);
       	$dict->ExecuteSQLArray($sqlarray);	
		
		$sqlarray = $dict->CreateIndexSQL('showid', cms_db_prefix().'module_simpleslider_showimages', 'showid');	
       	$dict->ExecuteSQLArray($sqlarray);			
		
		$sqlarray = $dict->CreateIndexSQL('showimage', cms_db_prefix().'module_simpleslider_showimages', array('showid', 'imageid'), array('UNIQUE'));	
       	$dict->ExecuteSQLArray($sqlarray);	
		
		$this->setPreference('defwidth', 400);
		$this->setPreference('defheight', 300);
		$this->setPreference('deffadetime', 4000);
		
		$this->CreatePermission('SimpleSlider Admin', 'SimpleSlider Admin');
		
		mkdir('../uploads/sliderimages/');
		
	}

	function VisibleToAdminUser()
	{
		return $this->CheckPermission('SimpleSlider Admin');
	}
	
	
	function Upgrade($oldversion, $newversion){
		
		$db = $this->cms->db;
		$dict = NewDataDictionary($db);
		
		switch($oldversion){
			case '0.2.1':
			case '0.2':
			case '0.1':
				$sqlarray=array(0=>'ALTER TABLE `'.cms_db_prefix().'module_simpleslider_shows` ADD `width` INT NOT NULL , ADD `height` INT NOT NULL , ADD `fadetime` INT NOT NULL ');
				$dict->ExecuteSQLArray($sqlarray);
				break;
		}
	}
	
	function Uninstall()
	{
		$db = $this->cms->db;
		$dict = NewDataDictionary( $db );
		
		$sqlarray = $dict->DropTableSQL( cms_db_prefix().'module_simpleslider_images' );
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->DropTableSQL( cms_db_prefix().'module_simpleslider_shows' );
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->DropTableSQL( cms_db_prefix().'module_simpleslider_showimages' );
		$dict->ExecuteSQLArray($sqlarray);
		//Remove the sequence
		$this->RemovePermission('SimpleSlider Admin');
		$this->RemovePreference();

	}

    function GetVersion()
    {
        return '0.3';
    }
	
	function GetHelp()
  	{
	return $this->Lang('help');
	}
	         
    function IsPluginModule()
    {
         return true;
    }
	
	function HasAdmin()	{
		return true;
	}

	function GetAdminSection() {
		return 'content';
	}
	

	function GetAdminDescription() {
		return 'Manage Simple Slider';
	}
	
	function GetAuthor(){
		return 'Burhan BAVKIR';
	}

	function GetAuthorEmail(){
		return 'info@bvkyazilim.com';
	}
	
	function displaySelectArray($selectlist, $id=0){

		foreach($selectlist as $key => $val){
	?>                      <option value="<?=$key?>"<? if($key==$id){ ?> selected="selected" <? } ?>><?=$val?></option>
    <?
    	}
	}
	
	
	function GenerateInsertSQL($table, $fields){
		$sql='INSERT INTO '.$table.' (';
		$keys=array_keys($fields);
		$values=' VALUES (';
		for($i=0; $i<(sizeof($fields)-1); $i++){
			$values.='"'.$fields[$keys[$i]].'", ';
			$sql.=$keys[$i].', ';
		}
		$values.='"'.$fields[$keys[$i]].'"); ';
		$sql.=$keys[$i].') '.$values;
		
		return $sql;
	}
	
	function GenerateUpdateSQL($table, $fields, $where){
		$sql='UPDATE '.$table.' SET ';
		$keys=array_keys($fields);
		for($i=0; $i<(sizeof($fields)-1); $i++){
			$sql.=$keys[$i].'="'.$fields[$keys[$i]].'", ';
		}
		$sql.=$keys[$i].'="'.$fields[$keys[$i]].'" WHERE '.$where.';';
		
		return $sql;
	}
	
}

?>

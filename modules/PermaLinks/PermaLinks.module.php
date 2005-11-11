<?php
# This is a string replacement module (c) 2005 by INTESOL SRL (Mihai Cimpoeru & Sorin Sbarnea)
# to enhance the project
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://cmsmadesimple.sf.net
#
#$Id$

class PermaLinks extends CMSModule
{

    function GetName()
  {
      return 'PermaLinks';
  }
  
    function GetVersion()
  {
      return '0.1';
  }
  
    function GetHelp($lang = 'en_US')
  {
	  return '<h3>What does this do?</h3>
        <p>This should enable writing of rewrite rules for apache</p>';
  }
  
    function GetDescription($lang = 'en_US')
  {
      return '.';
  }
  
    function GetChangeLog()
  {
      return '<p>0.1 First version</p>';
  }
  
    function GetAuthor()
  {
      return 'INTERSOL SRL';
  }
  
    function GetAuthorEmail()
  {
      return 'support@intersol.ro';
  }
  

    function IsContentModule()
  {
      return true;
  }
  
    function HasAdmin()
  {
    return false;
  }
  
    function InstallPostMessage()
  {
    return 'No further action required.';
  }
  
      function Install()
  {      
      $this->Audit( 0, 'Module_PermaLinks', 'Install V'.$this->getVersion());
  }
  
  function Upgrade($oldversion, $newversion) {
    
    $current_version = $oldversion;
    $ret = 0; 
    
    switch($current_version)
    {
      default:break;
    }
    if ($current_version == $this->GetVersion()) {
      $this->Audit( 0, 'Module_PermaLinks', 'Update V'.$oldversion.' &rarr; V'.$newversion);
      return FALSE;
    }
    else return 'Module_PermaLinks upgrade failed!';
  }
  
  
    function Uninstall()
  {
      $this->Audit( 0, 'Module_PermaLinks', 'Uninstall V'.$this->getVersion());
  }
  
    
    function ContentEditPost()
	{
		if (!$this->save_mod_rewrite_rules())
	  	return "Could not rewrite .htaccess.";
	}
    
    function ContentDeletePost()
	{
		if (!$this->save_mod_rewrite_rules())
	  	return "Could not rewrite .htaccess.";
	}
    
	function trailingslashit($string) {
	    if ( '/' != substr($string, -1)) {
	        $string .= '/';
	    }
	    return $string;
	}
	    
   	function mod_rewrite_rules() {
        global $config;
		$site_root = $config['root_url'];
		$home_root = $this->trailingslashit($config['root_path']);

    
		$rules = "<IfModule mod_rewrite.c>\n";
		$rules .= "RewriteEngine On\n";
		$rules .= "RewriteBase \"$home_root\"\n";
		$this->matches = '';
		$rewrite = $this->rewrite_rules();
		$num_rules = count($rewrite);
/*
 		// This is the old version, new version is trying static pages but not folders. So the virtual page will be used instead of the index of the folder.
		$rules .= "RewriteCond %{REQUEST_FILENAME} -f [OR]\n" .
			"RewriteCond %{REQUEST_FILENAME} -d\n" .
			"RewriteRule ^.*$ - [S=$num_rules]\n";
*/
		$rules .= "RewriteCond %{REQUEST_FILENAME} -f\n" .
			"RewriteRule ^.*$ - [S=$num_rules]\n";
		
		foreach ($rewrite as $match => $query) {
			// Apache 1.3 does not support the reluctant (non-greedy) modifier.
			$match = str_replace('.+?', '.+', $match);

			// If the match is unanchored and greedy, prepend rewrite conditions
			// to avoid infinite redirects and eclipsing of real files.
			if ($match == '(.+)/?$' || $match == '([^/]+)/?$' ) {
				//nada.
			}
/* I don't understand this section below. $home_root is a filesystem path, so the
   rewrite would fail, it seems to me. I'm no mod_rewrite expert, though.
   In any case, $this->index is undefined, so that first clause would never
   get triggered. So I chopped it. SjG
			if (strstr($query, $this->index)) {
				$rules .= 'RewriteRule ^' . $match . ' ' . $home_root . $query . " [QSA,L]\n";
			} else {
				$rules .= 'RewriteRule ^' . $match . ' ' . $site_root . $query . " [QSA,L]\n";
			}
*/          $rules .= 'RewriteRule ^' . $match . ' ' . $site_root . $query . " [QSA,L]\n";

		}
		$rules .= "</IfModule>\n";

		return $rules;
	}


	function save_mod_rewrite_rules() {
		global $config;

		if ( ! ((!file_exists($config['root_path'].'/.htaccess') && is_writable($config['root_path'] . '/')) || is_writable($config['root_path'].'/.htaccess')) )
		return;

		$rules = explode("\n", $this->mod_rewrite_rules());
		$this->insert_with_markers($config['root_path'].'/.htaccess', 'PermaLinks', $rules);

		return true;
	}
// --------------------
	function rewrite_rules() {
	$rewrite = array();

	global $db;
	$allcontent = ContentManager::GetAllContent();

	# defining variables
	$menu = "";
	$last_level = 0;
	$count = 0;
	$in_hr = 0;

	foreach ($allcontent as $onecontent)
	{
		#Handy little trick to figure out how deep in the tree we are
		#Remember, content comes to use in order of how it should be displayed in the tree already
		$depth = count(split('\.', $onecontent->Hierarchy()));

		#If hierarchy starts with the start_element (if it's set), then continue on
		if (isset($params['start_element']))
		{
			if (!(strpos($onecontent->Hierarchy(), $params['start_element']) !== FALSE && strpos($onecontent->Hierarchy(), $params['start_element']) == 0))
			{
				continue;
			}
		}

		#Now check to make sure we're not too many levels deep if number_of_levels is set
		if (isset($params['number_of_levels']))
		{
			$number_of_levels = $params['number_of_levels'] - 1;
			$base_level = 1;
			
			#Is start_element set?  If so, reset the base_level to it's level
			if (isset($params['start_element']))
			{
				$base_level = count(split('\.', $params['start_element']));
			}

			#If this element's level is more than base_level + number_of_levels, then scratch it
			if ($base_level + $number_of_levels < $depth)
			{
				continue;
			}
		}

		if (!$onecontent->Active())
		{
			continue;
		}

		if ($onecontent->Type() == 'separator')
		{
			continue;
		}

		if ($onecontent->Type() == 'sectionheader')
		{
			if ($in_hr == 1)
			{
				$in_hr = 0;
			}
            $rewrite[$onecontent->MenuText() . '/?$']="/index.php?page=" . $onecontent->MenuText();

			if ($count > 0 && $in_hr == 0)
			{
				$in_hr = 1;
			}
		}
		else
		{
			$menu .= "<li><h2><a href=\"".$onecontent->GetURL()."\">".$onecontent->MenuText();
			if ($onecontent->Name() != '')
			{
            $rewrite[ $onecontent->Alias() . '/?$']="/index.php?page=" . $onecontent->Alias();

			}
			$in_hr = 1;
			$last_level = $depth;
		}
		$count++;
	}

		return $rewrite;
	}
// --------------------

// Inserts an array of strings into a file (.htaccess), placing it between
// BEGIN and END markers.  Replaces existing marked info.  Retains surrounding
// data.  Creates file if none exists.
// Returns true on write success, false on failure.
function insert_with_markers($filename, $marker, $insertion) {
	if (!file_exists($filename) || is_writeable($filename)) {
		if (!file_exists($filename)) {
			$markerdata = '';
		} else {
			$markerdata = explode("\n", implode('', file($filename)));
		}

		$f = fopen($filename, 'w');
		$foundit = false;
		if ($markerdata) {
			$state = true;
			$newline = '';
			foreach($markerdata as $markerline) {
				if (strstr($markerline, "# BEGIN {$marker}")) $state = false;
				if ($state) fwrite($f, "{$newline}{$markerline}");
				if (strstr($markerline, "# END {$marker}")) {
					fwrite($f, "{$newline}# BEGIN {$marker}");
					if(is_array($insertion)) foreach($insertion as $insertline) fwrite($f, "{$newline}{$insertline}");
					fwrite($f, "{$newline}# END {$marker}");
					$state = true;
					$foundit = true;
				}
				$newline = "\n";
			}
		}
		if (!$foundit) {
			fwrite($f, "# BEGIN {$marker}\n");
			foreach($insertion as $insertline) fwrite($f, "{$insertline}\n");
			fwrite($f, "# END {$marker}");				
		}
		fclose($f);
		return true;
	} else {
		return false;
	}
}

// Returns an array of strings from a file (.htaccess) from between BEGIN
// and END markers.
function extract_from_markers($filename, $marker) {
	$result = array();

	if (!file_exists($filename)) {
		return $result;
	}

	if($markerdata = explode("\n", implode('', file($filename))));
	{
		$state = false;
		foreach($markerdata as $markerline) {
			if(strstr($markerline, "# END {$marker}"))	$state = false;
			if($state) $result[] = $markerline;
			if(strstr($markerline, "# BEGIN {$marker}")) $state = true;
		}
	}

	return $result;
}
	
    
}

?>
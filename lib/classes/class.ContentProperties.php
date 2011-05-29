<?php
# CMS - CMS Made Simple
# (c)2004 by Ted Kulp (tedkulp@users.sf.net)
# This project's homepage is: http://cmsmadesimple.org
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# BUT withOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id: class.content.inc.php 6905 2011-02-20 22:23:40Z calguy1000 $

/**
 * Class to represent content properties.  These are pretty much
 * separate beings that get used by a content object instance.
 *
 * @since		0.8
 * @package		CMS
 */
class ContentProperties
{
    var $mPropertyNames;
    var $mPropertyTypes;
    var $mPropertyValues;
     
    /**
     * The (content type specific) allowed properties of the content.
    */
    var $mAllowedPropertyNames;

    /**
     * Generic constructor. Runs the SetInitialValues fuction.
     */
	function ContentProperties()
	{
		$this->SetInitialValues();
		$this->SetAllowedPropertyNames(NULL);
	}

    /**
     * Sets object to some sane initial values
     */
	function SetInitialValues()
	{
		$this->mPropertyNames = array();
		$this->mPropertyTypes = array();
		$this->mPropertyValues = array();
	}

	function HasProperty($name)
	{
		#debug_buffer($this->mPropertyNames);
		if (!isset($this->mPropertyNames))
		  $this->mPropertyNames = array();
		return in_array($name, $this->mPropertyNames);
	}

	function Add($type, $name, $defaultvalue='')
	{
		//Handle names separately
		if (!in_array($name, $this->mPropertyNames))
		{
			$this->mPropertyNames[] = $name;
		}
		if (!array_key_exists($name, $this->mPropertyValues))
		{
			$this->mPropertyTypes[$name] = $type;
			$this->mPropertyValues[$name] = $defaultvalue;
		}
	}

	function GetValue($name)
	{
		if (in_array($name, $this->mPropertyNames))
		{
			if (count($this->mPropertyValues) > 0)
			{
				if (array_key_exists($name, $this->mPropertyValues))
				{
					return $this->mPropertyValues[$name];
				}
			}
		}
	}

	function SetValue($name, $value)
	{
		if (count($this->mPropertyValues) > 0)
		{
			if (in_array($name, $this->mPropertyNames))
			{
				$this->mPropertyValues[$name] = $value;
			}
		}
	}

	function Load($content_id)
	{
	  if( $content_id <= 0 ) return;
		if (count($this->mPropertyNames) > 0)
		{
		  $gCms = cmsms();
		  global $sql_queries, $debug_errors;
		  $db = $gCms->GetDb();

			$query = "SELECT * FROM ".cms_db_prefix()."content_props WHERE content_id = ?";
			$dbresult = $db->Execute($query, array($content_id));

			while ($dbresult && !$dbresult->EOF)
			{
				$prop_name = $dbresult->fields['prop_name'];
//				if ($this->GetAllowedPropertyNames() == NULL || in_array($prop_name, $this->GetAllowedPropertyNames()))
//				{
					if (!in_array($prop_name, $this->mPropertyNames))
					{
						$this->mPropertyNames[] = $prop_name;
					}
					$this->mPropertyTypes[$prop_name] = $dbresult->fields['type'];
					$this->mPropertyValues[$prop_name] = $dbresult->fields['content'];
//				}
				$dbresult->MoveNext();
			}
			
			if ($dbresult) $dbresult->Close();
		}
	}

	function Save($content_id)
	{
		if (count($this->mPropertyValues) > 0)
		{
		  $gCms = cmsms();
		  global $sql_queries, $debug_errors;
		  $db = $gCms->GetDb();

			$config    = $gCms->GetConfig();
			$concat    =  '';
			$timestamp =  $db->DBTimeStamp(time());

			$insquery = "
				INSERT INTO ".cms_db_prefix()."content_props 
				(
					content_id, 
					type, 
					prop_name, 
					param1, 
					param2, 
					param3, 
					content,
					modified_date
				) 
					VALUES 
				(
					?,?,?,'','','',?,$timestamp
				)
			";

			foreach ($this->mPropertyValues as $key=>$value)
			{
//				if ($this->GetAllowedPropertyNames() == NULL || in_array($key, $this->GetAllowedPropertyNames()))
//				{
					$delquery = "DELETE FROM ".cms_db_prefix()."content_props WHERE content_id = ? AND prop_name = ?";
					$dbresult = $db->Execute($delquery, array($content_id, $key));
					if (true == $config["debug"])
					  {
					    $sql_queries .= "<p>".$db->sql."</p>\n";
					  }
					
					$sql_vars = array(
						$content_id,
						$this->mPropertyTypes[$key],
						$key,
						$this->mPropertyValues[$key]
					);
					$dbresult = $db->Execute($insquery, $sql_vars);
					if (true == $config["debug"])
					  {
					    $sql_queries .= "<p>".$db->sql."</p>\n";
					  }

					$concat .= $this->mPropertyValues[$key];

					if (! $dbresult)
					{
						if (true == $config["debug"])
						{
							# :TODO: Translate the error message
							$debug_errors .= "<p>Error updating content property</p>\n";
						}
					}
//				}
			}

			if ($concat != '')
			{
				do_cross_reference($content_id, 'content', $concat);
			}
		}
	}

    function Delete($content_id)
    {
	if (count($this->mPropertyValues) > 0)
	{
	  $gCms = cmsms();
	  global $sql_queries, $debug_errors;
	  $db = $gCms->GetDb();

	    $query = "DELETE FROM ".cms_db_prefix()."content_props WHERE content_id = ?";
	    $db->Execute($query, array($content_id));
	}
    }

    /**
     * Subclasses should fill this array with strings containing the name of
     * the allowed property
     * @param array
    */
    function SetAllowedPropertyNames($array)
    {
        $this->mAllowedPropertyNames = $array;
    }

    
    /**
     * Subclasses should fill this array with strings containing the name of
     * the allowed property
     * @return array
    */
    function GetAllowedPropertyNames()
    {
        return $this->mAllowedPropertyNames;
    }

} // end of class ContentProperties

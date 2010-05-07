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
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

function search_StemPhrase(&$module,$phrase)
{
  // strip out smarty tags
  $phrase = preg_replace('/\{.*?\}/', '', $phrase);
  
  // add spaces between tags
  $phrase = str_replace("<"," <",$phrase);
  $phrase = str_replace(">","> ",$phrase);
  
  // strip out html and php stuff
  $phrase = strip_tags($phrase);
  
  // escape meta characters
  $phrase = preg_quote($phrase);
  
  // split into words
  // strtolower isn't friendly to other charsets
  $phrase = preg_replace("/([A-Z]+)/e",
			 "strtolower('\\1')",
			 $phrase);
  //$words = preg_split('/[\s,!.()+-\/\\\\]+/', $phrase);
  $words = preg_split('/[\s,!.;:\?()+-\/\\\\]+/', $phrase);
  
  // ignore stop words
  $words = $module->RemoveStopWordsFromArray($words);
  
  $stemmer = new PorterStemmer();
  
  // stem words
  $stemmed_words = array();
  $stem_pref = $module->GetPreference('usestemming', 'false');
  foreach ($words as $word)
    {
      //trim words get rid of wrapping quotes
      $word = trim($word, ' \'"');
      
      if (strlen($word) <= 0)
	{
	  continue;
	}
      
      if ($stem_pref == 'true')
	$stemmed_words[] = $stemmer->stem($word, true);
      else
	$stemmed_words[] = $word;
    }
  
  return $stemmed_words;
}


function search_AddWords(&$obj, $module = 'Search', $id = -1, $attr = '', $content = '', $expires = NULL)
{
  $obj->DeleteWords($module, $id, $attr);
  $db =& $obj->GetDb();
		
  $non_indexable = strpos($content, NON_INDEXABLE_CONTENT);
  if( $non_indexable ) return;

  @$obj->SendEvent('SearchItemAdded', array($module, $id, $attr, &$content, $expires));
		
  if ($content != "")
    {		
      //Clean up the content
      $stemmed_words = $obj->StemPhrase($content);
      $words = array_count_values($stemmed_words);
		
      $q = "SELECT id FROM ".cms_db_prefix().'module_search_items WHERE module_name=?';
      $parms = array($module);

      if( $id != -1 )
	{
	  $q .= " AND content_id=?";
	  $parms[] = $id;
	}
      if( $attr != '' )
	{
	  $q .= " AND extra_attr=?";
	  $parms[] = $attr;
	}
      $dbresult = $db->Execute($q, $parms);
		
      if ($dbresult && $dbresult->RecordCount() > 0 && $row = $dbresult->FetchRow())
	{
	  $itemid = $row['id'];
	}
      else
	{
	  $itemid = $db->GenID(cms_db_prefix()."module_search_items_seq");
	  $db->Execute('INSERT INTO '.cms_db_prefix().'module_search_items (id, module_name, content_id, extra_attr, expires) VALUES (?,?,?,?,?)', array($itemid, $module, $id, $attr, ($expires != NULL ? trim($db->DBTimeStamp($expires), "'") : NULL) ));
	}
		
      foreach ($words as $word=>$count)
	{
	  $db->Execute('INSERT INTO '.cms_db_prefix().'module_search_index (item_id, word, count) VALUES (?,?,?)', array($itemid, $word, $count));
	}
    }
}

function search_DeleteWords(&$obj, $module = 'Search', $id = -1, $attr = '')
{
  $db =& $obj->GetDb();
  $parms = array( $module );
  $q = "DELETE FROM ".cms_db_prefix().'module_search_items WHERE module_name=?';
  if( $id != -1 )
    {
      $q .= " AND content_id=?";
      $parms[] = $id;
    }
  if( $attr != '' )
    {
      $q .= " AND extra_attr=?";
      $parms[] = $attr;
    }
  $db->Execute($q, $parms);
  $db->Execute('DELETE FROM '.cms_db_prefix().'module_search_index WHERE item_id NOT IN (SELECT id FROM '.cms_db_prefix().'module_search_items)');
  @$obj->SendEvent('SearchItemDeleted', array($module, $id, $attr));
}


function search_Reindex(&$module)
{
  @set_time_limit(999);
  $module->DeleteAllWords();
		
  global $gCms;
  $templateops =& $gCms->GetTemplateOperations();
  $alltemplates = $templateops->LoadTemplates();
  reset($alltemplates);
  while (list($key) = each($alltemplates))
    {
      $onetemplate =& $alltemplates[$key];
      //$module->EditTemplatePost($onetemplate);
      $params = array('template' => &$onetemplate,
		      'forceindexcontent'=>1);
      $module->DoEvent('Core', 'EditTemplatePost', $params);
    }

  $gcbops =& $gCms->GetGlobalContentOperations();
  $allblobs = $gcbops->LoadHtmlBlobs();
  reset($allblobs);
  while (list($key) = each($allblobs))
    {
      $oneblob =& $allblobs[$key];
      //$module->EditHtmlBlobPost($oneblob);
      $params = array('global_content' => &$oneblob);
      $module->DoEvent('Core', 'EditGlobalContentPost', $params);
    }

  foreach($gCms->modules as $key=>$value)
    {
      if ($gCms->modules[$key]['installed'] == true &&
	  $gCms->modules[$key]['active'] == true)
	{
	  if (method_exists($gCms->modules[$key]['object'], 'SearchReindex'))
	    {
	      $gCms->modules[$key]['object']->SearchReindex($module);
	    }
	}
    }
}


function search_DoEvent(&$module, $originator, $eventname, &$params )
{
  if ($originator == 'Core')
    {
      switch ($eventname)
	{
	case 'ContentEditPost':
	  $content =& $params['content'];					
	  if (!isset($content)) return;

	  $db =& $module->GetDb();
	  $q = "SELECT id FROM ".cms_db_prefix()."module_search_items WHERE
                  extra_attr = ? AND content_id = ?";
	  $template_indexed = $db->GetOne( $q, array( 'template', $content->TemplateId() ));
	  if( !$template_indexed )
	    {
	      $module->DeleteWords($module->GetName(), $content->Id(), 'content');
	      break;
	    }

	  //Only index content if it's active
	  // and searchable.
	  // assume by default that it is searchable
	  $tmp = $content->GetPropertyValue('searchable');
	  if( $tmp == '' ) $tmp = 1;
	  if ($content->Active() && $tmp )
	    {

	      //Weight the title and menu text higher
	      $text = str_repeat(' '.$content->Name(), 2) . ' ';
	      $text .= str_repeat(' '.$content->MenuText(), 2) . ' ';

	      $props = $content->Properties();
	      foreach ($props->mPropertyValues as $k=>$v)
		{
		  $text .= $v.' ';
		}

	      // here check for a string to see
	      // if module content is indexable at all
	      $non_indexable = strpos($text, NON_INDEXABLE_CONTENT);
	      if (! $non_indexable)
		{
		  $module->AddWords($module->GetName(), $content->Id(), 'content', $text);
		}
	      else
		{
		  $module->DeleteWords($module->GetName(), $content->Id(), 'content');
		}
	    }
	  else
	    {
	      //Just in case the active flag was turned off
	      $module->DeleteWords($module->GetName(), $content->Id(), 'content');
	    }
					
	  break;

	case 'ContentDeletePost':
	  $content =& $params['content'];

	  if (!isset($content)) return;

	  $module->DeleteWords($module->GetName(), $content->Id(), 'content');

	  break;
					
	case 'AddTemplatePost':
	  $template =& $params['template'];
					
	  if( $template->active != false )
	    $module->AddWords($module->GetName(), $template->id, 'template', $template->content);
	  else
	    $module->DeleteWords($module->GetName(), $template->id, 'template');
				
	  break;
					
	case 'EditTemplatePost':
	  $template =& $params['template'];

	  if( $template->active != false )
	    {
	      // here check for a string to see
	      // if this content is indexable at all
	      $non_indexable = strpos($template->content, NON_INDEXABLE_CONTENT);
		  
	      $db =& $module->GetDb();
		  
	      // check if the page was indexed already or not
	      $q = "SELECT id FROM ".cms_db_prefix()."module_search_items WHERE content_id = ?
			AND extra_attr = ?";
	      $was_indexed = $db->GetOne( $q, array( $template->id, 'template' ) );
		  
	      // find all of the (active) pages tied to a template
	      $q = "SELECT content_id FROM ".cms_db_prefix()."content WHERE active > 0 AND template_id = ?";
	      $dbresult =& $db->Execute( $q, array( $template->id ) );
	      if( ! $non_indexable )
		{
		  $module->AddWords($module->GetName(), $template->id, 'template', $template->content);
		}
	      else
		{
		  $module->DeleteWords($module->GetName(), $template->id, 'template');
		}
		  
	      if( ($non_indexable && $was_indexed) )
		{
		  // we can't index the template, and it was indexed
		  // meaning we need to delete all indexes from
		  // the children.
		  $q2 = "DELETE FROM ".cms_db_prefix()."module_search_items WHERE
				extra_attr = ? AND content_id  IN (";
		  $parms = array( 'content' );
		      
		  // delete them all from the index
		  while( $dbresult && !$dbresult->EOF )
		    {
		      $q2 .= "?,";
		      $parms[] = $dbresult->fields['content_id'];
		      $dbresult->MoveNext();
		    }
		  $q2 = substr($q2,0,strlen($q2)-1);
		  $q2 .= ")";
		      
		  $db->Execute( $q2, $parms );
		      
		  $db->Execute('DELETE FROM '.cms_db_prefix().'module_search_index WHERE item_id NOT IN (SELECT id FROM '.cms_db_prefix().'module_search_items)');
		}
	      else 
		{
		  if (!$non_indexable && !$was_indexed)
		    { 
		      // The template is indexable, and was not indexed previously
		      // so we have to index it's children.
		      while( $dbresult && !$dbresult->EOF )
			{
			  global $gCms;
			  $contentops =& $gCms->GetContentOperations();
			  $onecontent =& $contentops->LoadContentFromId($dbresult->fields['content_id']);
			  $parms = array('content'=>&$onecontent);
			  $module->DoEvent('Core','ContentEditPost',$parms);
			  $dbresult->MoveNext();
			}
		    }
		}
	    }
	  else
	    {
	      // template is inactive
	      $module->DeleteWords($module->GetName(), $template->id, 'template');
	    }
	  break;
					
	case 'DeleteTemplatePost':
	  $template =& $params['template'];
	  $module->DeleteWords($module->GetName(), $template->id, 'template');

	  break;
					
	case 'AddGlobalContentPost':
	  $global_content =& $params['global_content'];
	  $module->AddWords($module->GetName(), $global_content->id, 'global_content', $global_content->content);

	  break;
					
	case 'EditGlobalContentPost':
	  $global_content =& $params['global_content'];
	  $module->AddWords($module->GetName(), $global_content->id, 'global_content', $global_content->content);
	  break;
					
	case 'DeleteGlobalContentPost':
	  $global_content =& $params['global_content'];

	  $module->DeleteWords($module->GetName(), $global_content->id, 'global_content');

	  break;

	case 'ModuleUninstalled':
	  $module_name =& $params['name'];

	  $module->DeleteWords($module_name);

	  break;
	}
    }
}


#
#
?>

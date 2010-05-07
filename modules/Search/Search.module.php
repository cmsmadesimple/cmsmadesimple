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
#
#$Id: News.module.php 2114 2005-11-04 21:51:13Z wishy $

define( "NON_INDEXABLE_CONTENT", "<!-- pageAttribute: NotSearchable -->" );

class Search extends CmsModuleBase
{
	private static function _load_tools()
	{
		$fn = dirname(__FILE__).'/search.tools.php';
		include_once($fn);
	}

	function __construct()
	{
		parent::__construct();
	}
	
	function get_admin_description()
	{
		return $this->Lang('description');
	}

	public function visible_to_admin_user()
	{
		return $this->CheckPermission('Modify Site Preferences') ||
			$this->CheckPermission('Modify Templates');
	}

	public function setup()
	{
		$this->restrict_unknown_params();
		
		$this->create_parameter('inline', 'false', $this->lang('param_inline'), FILTER_VALIDATE_BOOLEAN);
		$this->create_parameter('passthru_*', 'null', $this->lang('param_passthru'), FILTER_SANITIZE_STRING);
		//$this->SetParameterType(CLEAN_REGEXP.'/passthru_.*/',CLEAN_STRING);
		$this->create_parameter('customfields_*', 'null', $this->lang('param_customfields'), FILTER_SANITIZE_STRING);
		//$this->SetParameterType(CLEAN_REGEXP.'/customfields_.*/',CLEAN_STRING);
		$this->create_parameter('modules', 'null', $this->lang('param_modules'), FILTER_SANITIZE_STRING);

		$this->create_parameter('resultpage', 'null', $this->lang('param_resultpage'), FILTER_SANITIZE_STRING);
		$this->create_parameter('searchtext', 'null', $this->Lang('param_searchtext'), FILTER_SANITIZE_STRING);
		//$this->SetParameterType('searchinput',CLEAN_STRING);

		$this->create_parameter('submit', $this->lang('searchsubmit'), $this->lang('param_submit'), FILTER_SANITIZE_STRING);
		//$this->SetParameterType('origreturnid',CLEAN_INT);

		$this->create_parameter('action', 'default', $this->Lang('param_action'), FILTER_SANITIZE_STRING);
		$this->create_parameter('pageid', 'null', $this->lang('param_pageid'), FILTER_SANITIZE_NUMBER_INT);
		$this->create_parameter('count', 'null', $this->lang('param_count'), FILTER_SANITIZE_NUMBER_INT);

		$this->create_parameter('search_method', 'get' ,$this->lang('search_method'), FILTER_SANITIZE_STRING);
	}

	public function DefaultStopWords()
	{
		return 'i, me, my, myself, we, our, ours, ourselves, you, your, yours, 
			yourself, yourselves, he, him, his, himself, she, her, hers, 
			herself, it, its, itself, they, them, their, theirs, themselves, 
			what, which, who, whom, this, that, these, those, am, is, are, 
			was, were, be, been, being, have, has, had, having, do, does, 
			did, doing, a, an, the, and, but, if, or, because, as, until, 
			while, of, at, by, for, with, about, against, between, into, 
			through, during, before, after, above, below, to, from, up, down, 
			in, out, on, off, over, under, again, further, then, once, here, 
			there, when, where, why, how, all, any, both, each, few, more, 
			most, other, some, such, no, nor, not, only, own, same, so, 
			than, too, very';
	}

	public function RemoveStopWordsFromArray($words)
	{
		$stop_words = preg_split("/[\s,]+/", $this->GetPreference('stopwords', $this->DefaultStopWords()));
		return array_diff($words, $stop_words);
	}

	public function DeleteWords($module = 'Search', $id = -1, $attr = '')
	{
		return CmsSearch::get_instance()->remove_content($module,$attr,$id);
	}

	public function DeleteAllWords($module = 'Search', $id = -1, $attr = '')
	{
		CmsSearch::get_instance()->create_index();		
		@$this->SendEvent('SearchAllItemsDeleted',array($module, $id, $attr));
	}

	public function get_change_log()
	{
		return @file_get_contents(dirname(__FILE__).'/changelog.inc');
	}

	/**
	* Reindexes the sites content
	* @deprecated Deprecated use CmsSearch::reindex();
	*/
	public function Reindex()
	{
		CmsSearch::get_instance()->reindex();
	}

	public function GetEventDescription( $eventname )
	{
		return $this->lang('eventdesc-' . $eventname);
	}

	public function GetEventHelp( $eventname )
	{
		return $this->lang('eventhelp-' . $eventname);
	}

	public function do_event($event_name, $params)
	{
		self::_load_tools();
		list($originator, $eventname) = explode(":", $event_name);
		return search_DoEvent($this, $originator, $eventname, $params);
	}
}

# vim:ts=4 sw=4 noet
?>

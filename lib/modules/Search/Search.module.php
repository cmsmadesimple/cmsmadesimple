<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#Visit our homepage at: http://www.cmsmadesimple.org
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
use CMSMS\HookManager;

include_once(dirname(__FILE__) . '/PorterStemmer.class.php');

define( "NON_INDEXABLE_CONTENT", "<!-- pageAttribute: NotSearchable -->" );

class Search extends CMSModule
{
    private function load_tools()
    {
        static $_loaded;
        if( !$_loaded ) {
            $fn = __DIR__.'/search.tools.php';
            include_once($fn);
            $_loaded = TRUE;
        }
    }

    public function LazyLoadFrontend()
    {
        return FALSE;
    }

    public function LazyLoadAdmin()
    {
        return FALSE;
    }

    public function GetName()
    {
        return 'Search';
    }

    public function GetFriendlyName()
    {
        return $this->Lang('search');
    }

    public function IsPluginModule()
    {
        return true;
    }

    public function HasAdmin()
    {
        return true;
    }

    public function GetVersion()
    {
        return '1.52';
    }

    public function MinimumCMSVersion()
    {
        return '2.2.903';
    }

    public function GetAdminDescription()
    {
        return $this->Lang('description');
    }

    public function VisibleToAdminUser()
    {
        return $this->CheckPermission('Modify Site Preferences');
    }

    public function GetHelp($lang='en_US')
    {
        return file_get_contents(__DIR__.'/help.inc');
    }

    public function GetAuthor()
    {
        return 'Ted Kulp';
    }

    public function GetAuthorEmail()
    {
        return 'ted@cmsmadesimple.org';
    }

    public function GetChangeLog()
    {
        return @file_get_contents(__DIR__.'/changelog.inc');
    }

    public function InitializeCommon()
    {
        HookManager::add_hook('Core::ContentEditPost', [ $this, 'hook_ContentEditPost' ]);
        HookManager::add_hook('Core::ContentDeletePost', [ $this, 'hook_ContentDeletePost' ]);
        HookManager::add_hook('Core::ModuleUninstalled', [ $this, 'hook_ModuleUninstalled'] );
    }

    public function InitializeAdmin()
    {
        $this->CreateParameter('inline','false',$this->Lang('param_inline'));
        $this->CreateParameter('passthru_*','null',$this->Lang('param_passthru'));
        $this->CreateParameter('modules','null',$this->Lang('param_modules'));
        $this->CreateParameter('resultpage', 'null', $this->Lang('param_resultpage'));
        $this->CreateParameter('searchtext','null',$this->Lang('param_searchtext'));
        $this->CreateParameter('detailpage','null',$this->Lang('param_detailpage'));
        $this->CreateParameter('submit',$this->Lang('searchsubmit'),$this->Lang('param_submit'));
        //$this->CreateParameter('action','default',$this->Lang('param_action'));
        //$this->CreateParameter('pageid','null',$this->Lang('param_pageid'));
        //$this->CreateParameter('count','null',$this->Lang('param_count'));
        $this->CreateParameter('use_like',false,$this->Lang('param_uselike'));
        $this->CreateParameter('search_method','get',$this->Lang('search_method'));
        $this->CreateParameter('formtemplate','',$this->Lang('param_formtemplate'));
        $this->CreateParameter('resulttemplate','',$this->Lang('param_resulttemplate'));
    }

    public function InitiaslizeFrontend()
    {
        $this->RegisterRoute('/[Ss]earch\/(?P<returnid>[0-9]+)$/',['action'=>'dosearch']);
    }

    public function hook_ContentEditPost($params)
    {
        $this->load_tools();
        list($originator,$event) = ['Core','ContentEditPost'];
        search_DoEvent($this,$originator,$event,$params);
    }

    public function hook_ContentDeletePost($params)
    {
        $this->load_tools();
        list($originator,$event) = ['Core','ContentDeletePost'];
        search_DoEvent($this,$originator,$event,$params);
    }

    public function hook_AddTemplatePost($params)
    {
        $this->load_tools();
        list($originator,$event) = ['Core','AddTemplatePost'];
        search_DoEvent($this,$originator,$event,$params);
    }

    public function hook_EditTemplatePost($params)
    {
        $this->load_tools();
        list($originator,$event) = ['Core','EditTemplatePost'];
        search_DoEvent($this,$originator,$event,$params);
    }

    public function hook_DeleteTemplatePost($params)
    {
        $this->load_tools();
        list($originator,$event) = ['Core','DeleteTemplatePost'];
        search_DoEvent($this,$originator,$event,$params);
    }

    public function hook_ModuleUninstalled($params)
    {
        $this->load_tools();
        list($originator,$event) = ['Core','ModuleUninstalled'];
        search_DoEvent($this,$originator,$event,$params);
    }

    public function InitializeFrontend()
    {
        // $this->RestrictUnknownParams();

        $this->SetParameterType('inline',CLEAN_STRING);
        $this->SetParameterType(CLEAN_REGEXP.'/passthru_.*/',CLEAN_STRING);
        $this->SetParameterType('modules',CLEAN_STRING);
        $this->SetParameterType('resultpage',CLEAN_STRING);
        $this->SetParameterType('detailpage',CLEAN_STRING);
        $this->SetParameterType('searchtext',CLEAN_STRING);
        $this->SetParameterType('searchinput',CLEAN_STRING);
        $this->SetParameterType('submit',CLEAN_STRING);
        $this->SetParameterType('origreturnid',CLEAN_INT);
        $this->SetParameterType('pageid',CLEAN_INT);
        $this->SetParameterType('count',CLEAN_INT);
        $this->SetParameterType('use_like',CLEAN_INT);
        $this->SetParameterType('search_method',CLEAN_STRING);
        $this->SetParameterType('formtemplate',CLEAN_STRING);
        $this->SetParameterType('resulttemplate',CLEAN_STRING);
    }

    protected function GetSearchHtmlTemplate()
    {
        return file_get_contents($this->GetModulePath().'/templates/orig_searchform.tpl');
    }

    protected function GetResultsHtmlTemplate()
    {
        return file_get_contents($this->GetModulePath().'/templates/orig_resultlist.tpl');
    }

    protected function DefaultStopWords()
    {
        return $this->Lang('default_stopwords');
    }

    public function RemoveStopWordsFromArray($words)
    {
        $stop_words = preg_split("/[\s,]+/", $this->GetPreference('stopwords', $this->DefaultStopWords()));
        return array_diff($words, $stop_words);
    }

    public function StemPhrase($phrase, $filter_stopwords, $do_stemming)
    {
        $this->load_tools();
        return search_StemPhrase($this, $phrase, $filter_stopwords, $do_stemming);
    }

    public function AddWords($module = 'Search', $id = -1, $attr = '', $content = '', $expires = NULL)
    {
        $this->load_tools();
        return search_AddWords($this,$module,$id,$attr,$content,$expires);
    }

    public function DeleteWords($module = 'Search', $id = -1, $attr = '')
    {
        $this->load_tools();
        return search_DeleteWords($this,$module,$id,$attr);
    }

    public function DeleteAllWords($module = 'Search', $id = -1, $attr = '')
    {
        $db = $this->GetDb();
        $db->Execute('TRUNCATE '.CMS_DB_PREFIX.'module_search_index');
        $db->Execute('TRUNCATE '.CMS_DB_PREFIX.'module_search_items');
        \CMSMS\HookManager::do_hook('Search::SearchAllItemsDeleted' );
    }

    public function Reindex()
    {
        $this->load_tools();
        return search_Reindex($this);
    }

    public function HasCapability($capability,$params = array())
    {
        switch( $capability ) {
            case CmsCoreCapabilities::SEARCH_MODULE:
            case CmsCoreCapabilities::PLUGIN_MODULE:
            case 'clicommands':
                return true;
        }
        return FALSE;
    }

    public static function page_type_lang_callback($str)
    {
        $mod = cms_utils::get_module('Search');
        if( is_object($mod) ) return $mod->Lang('type_'.$str);
    }

    public static function reset_page_type_defaults(CmsLayoutTemplateType $type)
    {
        if( $type->get_originator() != 'Search' ) throw new CmsLogicException('Cannot reset contents for this template type');

        $mod = cms_utils::get_module('Search');
        if( !is_object($mod) ) return;
        switch( $type->get_name() ) {
            case 'searchform':
                return $mod->GetSearchHtmlTemplate();
            case 'searchresults':
                return $mod->GetResultsHtmlTemplate();
        }
    }

    public function get_cli_commands( $app )
    {
        if( ! $app instanceof \CMSMS\CLI\App ) throw new \LogicException(__METHOD__.' Called from outside of cmscli');
        if( !class_exists('\\CMSMS\\CLI\\GetOptExt\\Command') ) throw new \LogicException(__METHOD__.' Called from outside of cmscli');

        $out = [];
        $out[] = new \Search\ReindexCommand( $app );
        return $out;
    }
} // class

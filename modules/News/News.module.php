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
if( !isset($gCms) ) exit;

class News extends CMSModule
{
    function GetName() { return 'News'; }
    function GetFriendlyName() { return $this->Lang('news'); }
    function IsPluginModule() { return true; }
    function HasAdmin() { return true; }
    function GetVersion() { return '2.51.13'; }
    function MinimumCMSVersion() { return '2.1.6'; }
    function GetAdminDescription() { return $this->Lang('description'); }
    function GetAdminSection() { return 'content'; }
    function AllowSmartyCaching() { return TRUE; }
    function LazyLoadFrontend() { return TRUE; }
    function LazyLoadAdmin() { return TRUE; }
    function InstallPostMessage() { return $this->Lang('postinstall');  }
    function GetHelp() { return $this->Lang('help'); }
    function GetAuthor() { return 'Ted Kulp'; }
    function GetAuthorEmail() { return 'wishy@cmsmadesimple.org'; }
    function GetChangeLog() { return file_get_contents(dirname(__FILE__).'/changelog.inc'); }
    function GetEventDescription( $eventname ) { return $this->lang('eventdesc-' . $eventname); }
    function GetEventHelp( $eventname ) { return $this->lang('eventhelp-' . $eventname); }

    function InitializeFrontend()
    {
        $this->RestrictUnknownParams();

        $this->SetParameterType('pagelimit',CLEAN_INT);
        $this->SetParameterType('browsecat',CLEAN_INT);
        $this->SetParameterType('showall',CLEAN_INT);
        $this->SetParameterType('showarchive',CLEAN_INT);
        $this->SetParameterType('sortasc',CLEAN_STRING); // should be int, or boolean
        $this->SetParameterType('sortby',CLEAN_STRING);
        $this->SetParameterType('detailpage',CLEAN_STRING);
        $this->SetParameterType('detailtemplate',CLEAN_STRING);
        $this->SetParameterType('formtemplate',CLEAN_STRING);
        $this->SetParameterType('browsecattemplate',CLEAN_STRING);
        $this->SetParameterType('summarytemplate',CLEAN_STRING);
        $this->SetParameterType('moretext',CLEAN_STRING);
        $this->SetParameterType('category',CLEAN_STRING);
        $this->SetParameterType('category_id',CLEAN_STRING);
        $this->SetParameterType('number',CLEAN_INT);
        $this->SetParameterType('start',CLEAN_INT);
        $this->SetParameterType('pagenumber',CLEAN_INT);
        $this->SetParameterType('articleid',CLEAN_INT);
        $this->SetParameterType('origid',CLEAN_INT);
        $this->SetParameterType('showtemplate',CLEAN_STRING);
        $this->SetParameterType('assign',CLEAN_STRING);
        $this->SetParameterType('inline',CLEAN_STRING);
        $this->SetParameterType('preview',CLEAN_STRING);
        $this->SetParameterType('idlist',CLEAN_STRING);

        // form parameters
        $this->SetParameterType('submit',CLEAN_STRING);
        $this->SetParameterType('cancel',CLEAN_STRING);
        $this->SetParameterType('category',CLEAN_STRING);
        $this->SetParameterType('title',CLEAN_STRING);
        $this->SetParameterType('content',CLEAN_STRING);
        $this->SetParameterType('summary',CLEAN_STRING);
        $this->SetParameterType('extra',CLEAN_STRING);
        $this->SetParameterType('postdate',CLEAN_STRING);
        $this->SetParameterType('postdate_Hour',CLEAN_STRING);
        $this->SetParameterType('postdate_Minute',CLEAN_STRING);
        $this->SetParameterType('postdate_Second',CLEAN_STRING);
        $this->SetParameterType('postdate_Month',CLEAN_STRING);
        $this->SetParameterType('postdate_Day',CLEAN_STRING);
        $this->SetParameterType('postdate_Year',CLEAN_STRING);
        $this->SetParameterType('startdate',CLEAN_STRING);
        $this->SetParameterType('startdate_Hour',CLEAN_STRING);
        $this->SetParameterType('startdate_Minute',CLEAN_STRING);
        $this->SetParameterType('startdate_Second',CLEAN_STRING);
        $this->SetParameterType('startdate_Month',CLEAN_STRING);
        $this->SetParameterType('startdate_Day',CLEAN_STRING);
        $this->SetParameterType('startdate_Year',CLEAN_STRING);
        $this->SetParameterType('enddate',CLEAN_STRING);
        $this->SetParameterType('enddate_Hour',CLEAN_STRING);
        $this->SetParameterType('enddate_Minute',CLEAN_STRING);
        $this->SetParameterType('enddate_Second',CLEAN_STRING);
        $this->SetParameterType('enddate_Month',CLEAN_STRING);
        $this->SetParameterType('enddate_Day',CLEAN_STRING);
        $this->SetParameterType('enddate_Year',CLEAN_STRING);
        $this->SetParameterType('useexp',CLEAN_INT);
        $this->SetParameterType('input_category',CLEAN_STRING);
        $this->SetParameterType('category_id',CLEAN_INT);

        $this->SetParameterType(CLEAN_REGEXP.'/news_customfield_.*/',CLEAN_STRING);
        $this->SetParameterType('junk',CLEAN_STRING);
    }


    function InitializeAdmin()
    {
        $this->CreateParameter('pagelimit', 1000, $this->Lang('help_pagelimit'));
        $this->CreateParameter('browsecat', 0, $this->lang('helpbrowsecat'));
        $this->CreateParameter('showall', 0, $this->lang('helpshowall'));
        $this->CreateParameter('showarchive', 0, $this->lang('helpshowarchive'));
        $this->CreateParameter('sortasc', 'true', $this->lang('helpsortasc'));
        $this->CreateParameter('sortby', 'news_date', $this->lang('helpsortby'));
        $this->CreateParameter('detailpage', 'pagealias', $this->lang('helpdetailpage'));
        $this->CreateParameter('detailtemplate', '', $this->lang('helpdetailtemplate'));
        $this->CreateParameter('summarytemplate', '', $this->lang('helpsummarytemplate'));
        $this->CreateParameter('formtemplate', '', $this->lang('helpformtemplate'));
        $this->CreateParameter('browsecattemplate', '', $this->lang('helpbrowsecattemplate'));
        $this->CreateParameter('moretext', 'more...', $this->lang('helpmoretext'));
        $this->CreateParameter('category', 'category', $this->lang('helpcategory'));
        $this->CreateParameter('number', 100000, $this->lang('helpnumber'));
        $this->CreateParameter('start', 0, $this->lang('helpstart'));
        $this->CreateParameter('action','default',$this->Lang('helpaction'));
        $this->CreateParameter('articleid','',$this->Lang('help_articleid'));
        $this->CreateParameter('idlist','',$this->Lang('help_idlist'));
    }

    function VisibleToAdminUser()
    {
        return $this->CheckPermission('Modify News') || $this->CheckPermission('Modify Site Preferences') ||
            $this->CheckPermission('Approve News');
    }

    function GetDfltEmailTemplate()
    {
        $text = "A new news article has been posted to your website.  The details are as follows:\n";
        $text .= "Title:      {\$title}\n";
        $text .= "IP Address: {\$ipaddress}\n";
        $text .= "Summary:    {\$summary|strip_tags}\n";
        $text .= "Start Date: {\$startdate|localedate_format}\n";
        $text .= "End Date:   {\$enddate|localedate_format}\n";
        return $text;
    }

    function SearchResultWithParams($returnid, $articleid, $attr = '', $params = '')
    {
        $gCms = CmsApp::get_instance();
        $result = array();

        if ($attr == 'article') {
            $db = $this->GetDb();
            $q = "SELECT news_title,news_url FROM ".CMS_DB_PREFIX."module_news WHERE news_id = ?";
            $row = $db->GetRow( $q, array( $articleid ) );

            if ($row) {
                //0 position is the prefix displayed in the list results.
                $result[0] = $this->GetFriendlyName();

                //1 position is the title
                $result[1] = $row['news_title'];

                //2 position is the URL to the title.
                $detailpage = $returnid;
                if( isset($params['detailpage']) ) {
                    $manager = $gCms->GetHierarchyManager();
                    $node = $manager->sureGetNodeByAlias($params['detailpage']);
                    if (isset($node)) {
                        $detailpage = $node->getID();
                    }
                    else {
                        $node = $manager->sureGetNodeById($params['detailpage']);
                        if (isset($node)) $detailpage = $params['detailpage'];
                    }
                }
                if( $detailpage == '' ) $detailpage = $returnid;

                $detailtemplate = '';
                if( isset($params['detailtemplate']) ) {
                    $manager = $gCms->GetHierarchyManager();
                    $node = $manager->sureGetNodeByAlias($params['detailtemplate']);
                    if (isset($node)) $detailtemplate = '/d,' . $params['detailtemplate'];
                }

                $prettyurl = $row['news_url'];
                if( $row['news_url'] == '' ) {
                    $aliased_title = munge_string_to_url($row['news_title']);
                    $prettyurl = 'news/' . $articleid.'/'.$detailpage."/$aliased_title".$detailtemplate;
                }

                $parms = array();
                $parms['articleid'] = $articleid;
                if( isset($params['detailtemplate']) ) $parms['detailtemplate'] = $params['detailtemplate'];
                $result[2] = $this->CreateLink('cntnt01', 'detail', $detailpage, '', $parms ,'', true, false, '', true, $prettyurl);
            }
        }

        return $result;
    }

    function SearchReindex(&$module)
    {
        $db = $this->GetDb();

        $query = 'SELECT * FROM '.CMS_DB_PREFIX.'module_news WHERE searchable = 1 AND status = ? ORDER BY news_date';
        $result = $db->Execute($query,array('published'));

        while ($result && !$result->EOF) {
            if ($result->fields['status'] == 'published') {
                $module->AddWords($this->GetName(),
                                  $result->fields['news_id'], 'article',
                                  $result->fields['news_data'] . ' ' . $result->fields['summary'] . ' ' . $result->fields['news_title'] . ' ' . $result->fields['news_title'],
                                  ($result->fields['end_time'] != NULL && $this->GetPreference('expired_searchable',0) == 0) ?  $db->UnixTimeStamp($result->fields['end_time']) : NULL);
            }
            $result->MoveNext();
        }
    }


    public function GetFieldTypes()
    {
        $items = [ 'textbox'=>$this->Lang('textbox'),
                   'checkbox'=>$this->Lang('checkbox'),
                   'textarea'=>$this->Lang('textarea'),
                   'dropdown'=>$this->Lang('dropdown'),
                   'linkedfile'=>$this->Lang('linkedfile'),
                   'file'=>$this->Lang('file') ];
        return $items;
    }

    function GetTypesDropdown( $id, $name, $selected = '' )
    {
        $items = $this->GetFieldTypes();
        return $this->CreateInputDropdown($id, $name, array_flip($items), -1, $selected);
    }

    public function get_tasks()
    {
        if( !$this->GetPreference('alert_drafts',0) ) return;
        $out = array();
        $out[] = new \News\CreateDraftAlertTask();
        return $out;
    }

    function GetNotificationOutput($priority = 2)
    {
        // if this user has permission to change News articles from
        // Draft to published, and there are draft news articles
        // then display a nice message.
        // this is a priority 2 item.
        if( $priority >= 2 ) {
            $output = array();
            if( $this->CheckPermission('Approve News') ) {
                $db = $this->GetDb();
                $query = 'SELECT count(news_id) FROM '.CMS_DB_PREFIX.'module_news n WHERE status != \'published\'
                  AND (end_time IS NULL OR end_time > NOw())';
                $count = $db->GetOne($query);
                if( $count ) {
                    $obj = new StdClass;
                    $obj->priority = 2;
                    $link = $this->CreateLink('m1_','defaultadmin','', $this->Lang('notify_n_draft_items_sub',$count));
                    $obj->html = $this->Lang('notify_n_draft_items',$link);
                    $output[] = $obj;
                }
            }
        }
        return $output;
    }

    public function CreateStaticRoutes()
    {
        cms_route_manager::del_static('',$this->GetName());

        $db = \CmsApp::get_instance()->GetDb();
        $str = $this->GetName();
        $c = strtoupper($str[0]);
        $x = substr($str,1);
        $x1 = '['.$c.strtolower($c).']'.$x;

        $route = new CmsRoute('/'.$x1.'\/(?P<articleid>[0-9]+)\/(?P<returnid>[0-9]+)\/(?P<junk>.*?)\/d,(?P<detailtemplate>.*?)$/',
                              $this->GetName());
        cms_route_manager::add_static($route);
        $route = new CmsRoute('/'.$x1.'\/(?P<articleid>[0-9]+)\/(?P<returnid>[0-9]+)\/(?P<junk>.*?)$/',$this->GetName());
        cms_route_manager::add_static($route);
        $route = new CmsRoute('/'.$x1.'\/(?P<articleid>[0-9]+)\/(?P<returnid>[0-9]+)$/',$this->GetName());
        cms_route_manager::add_static($route);
        $route = new CmsRoute('/'.$x1.'\/(?P<articleid>[0-9]+)$/',$this->GetName(),
                              array('returnid'=>$this->GetPreference('detail_returnid',-1)));
        cms_route_manager::add_static($route);

        $query = 'SELECT news_id,news_url FROM '.CMS_DB_PREFIX.'module_news WHERE status = ? AND news_url != ? AND '
            . '('.$db->ifNull('start_time',$db->DbTimeStamp(1)).' < NOW()) AND '
            . '(('.$db->IfNull('end_time',$db->DbTimeStamp(1)).' = '.$db->DbTimeStamp(1).') OR (end_time > NOW()))';
        $query .= ' ORDER BY news_date DESC';
        $tmp = $db->GetArray($query,array('published',''));

        if( is_array($tmp) ) {
            foreach( $tmp as $one ) {
                news_admin_ops::register_static_route($one['news_url'],$one['news_id']);
            }
        }
    }

    public static function page_type_lang_callback($str)
    {
        $mod = cms_utils::get_module('News');
        if( is_object($mod) ) return $mod->Lang('type_'.$str);
    }

    public static function template_help_callback($str)
    {
        $str = trim($str);
        $mod = cms_utils::get_module('News');
        if( is_object($mod) ) {
            $file = $mod->GetModulePath().'/doc/tpltype_'.$str.'.inc';
            if( is_file($file) ) return file_get_contents($file);
        }
    }

    public static function reset_page_type_defaults(CmsLayoutTemplateType $type)
    {
        if( $type->get_originator() != 'News' ) throw new CmsLogicException('Cannot reset contents for this template type');

        $fn = null;
        switch( $type->get_name() ) {
        case 'summary':
            $fn = 'orig_summary_template.tpl';
            break;

        case 'detail':
            $fn = 'orig_detail_template.tpl';
            break;

        case 'form':
            $fn = 'orig_form_template.tpl';
            break;

        case 'browsecat':
            $fn = 'browsecat.tpl';
        }

        $fn = cms_join_path(__DIR__,'templates',$fn);
        if( file_exists($fn) ) return @file_get_contents($fn);
    }

    public function HasCapability($capability, $params = array())
    {
        switch( $capability ) {
        case CmsCoreCapabilities::PLUGIN_MODULE:
        case CmsCoreCapabilities::ADMINSEARCH:
        case CmsCoreCapabilities::TASKS:
            return TRUE;
        }
        return FALSE;
    }

    public function get_adminsearch_slaves()
    {
        return array('News_AdminSearch_slave');
    }

    public function GetAdminMenuItems()
    {
        $out = array();
        if( $this->VisibleToAdminUser() ) $out[] = CmsAdminMenuItem::from_module($this);

        if( $this->CheckPermission('Modify Site Preferences') ) {
            $obj = new CmsAdminMenuItem();
            $obj->module = $this->GetName();
            $obj->section = 'siteadmin';
            $obj->title = $this->Lang('title_news_settings');
            $obj->description = $this->Lang('desc_news_settings');
            $obj->action = 'admin_settings';
            $out[] = $obj;
        }
        return $out;
    }
} // end of class

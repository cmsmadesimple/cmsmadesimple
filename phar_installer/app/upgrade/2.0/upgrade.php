<?php

set_time_limit(3600);
status_msg('Fixing errors with deprecated plugins in versions prior to CMSMS 2.0');
$fn = $destdir.'/plugins/function.process_pagedata.php';
verbose_msg('deleting file '.$fn);
if( file_exists($fn) ) {
  @unlink($fn);
}
status_msg('Upgrading database for CMSMS 2.0');

$gCms = cmsms();
$dbdict = NewDataDictionary($db);
$taboptarray = array('mysql' => 'TYPE=MyISAM');

verbose_msg('updating structure of content tabless');
$sqlarray = $dbdict->DropColumnSQL(CMS_DB_PREFIX.'content',array('collaapsed','markup'));
$return = $dbdict->ExecuteSQLArray($sqlarray);
$sqlarray = $dbdict->AlterColumnSQL(CMS_DB_PREFIX.'content_props', 'content X2');
$return = $dbdict->ExecuteSQLArray($sqlarray);
$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_content_by_modified', CMS_DB_PREFIX."content", 'modified_date');
$return = $dbdict->ExecuteSQLArray($sqlarray);

verbose_msg('add index to the module plugins table');
$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_smp_module', CMS_DB_PREFIX."module_smarty_plugins", 'module');
$return = $dbdict->ExecuteSQLArray($sqlarray);

verbose_msg('updating structure of the permissions table');
$sqlarray = $dbdict->AddColumnSQL(CMS_DB_PREFIX.'permissions','permission_source C(255)');
$return = $dbdict->ExecuteSQLArray($sqlarray);

verbose_msg('add index to user groups table');
$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_ug_keys', CMS_DB_PREFIX.'user_groups ','group_id, user_id',array('UNIQUE'));
$return = $dbdict->ExecuteSQLArray($sqlarray);

verbose_msg('deleting old events');
$tmp = array('AddGlobalContentPre','AddGlobalContentPost','EditGlobalContentPre','EditGlobalContentPost',
	     'DeleteGlobalContentPre','DeleteGlobalContentPost','GlobalContentPreCompile','GlobalContentPostCompile',
	     'ContentStylesheet');
$query = 'DELETE FROM '.CMS_DB_PREFIX.'events WHERE originator = \'Core\' AND event_name IN ('.implode(',',$tmp).')';
$return = $db->Execute($query);

// create new events
verbose_msg('creating new events');
Events::CreateEvent('Core','AddTemplateTypePre');
Events::CreateEvent('Core','AddTemplateTypePost');
Events::CreateEvent('Core','EditTemplateTypePre');
Events::CreateEvent('Core','EditTemplateTypePost');
Events::CreateEvent('Core','DeleteTemplateTypePre');
Events::CreateEvent('Core','DeleteTemplateTypePost');
Events::CreateEvent('Core','AddDesignPre');
Events::CreateEvent('Core','AddDesignPost');
Events::CreateEvent('Core','EditDesignPre');
Events::CreateEvent('Core','EditDesignPost');
Events::CreateEvent('Core','DeleteDesignPre');
Events::CreateEvent('Core','DeleteDesignPost');

// create new tables
verbose_msg('create table '.CmsLayoutTemplateType::TABLENAME);
$flds = "
         id I KEY AUTO,
         originator C(50) NOTNULL,
         name C(100) NOTNULL,
         has_dflt I1,
         dflt_contents X2,
         description X,
         lang_cb     C(255),
         dflt_content_cb C(255),
         requires_contentblocks I1,
         owner   I,
         created I,
         modified I";
$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX.CmsLayoutTemplateType::TABLENAME, $flds, $taboptarray);
$return = $dbdict->ExecuteSQLArray($sqlarray);

$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_layout_tpl_type_1', CMS_DB_PREFIX.CmsLayoutTemplateType::TABLENAME, 'originator,name', array('UNIQUE'));
$return = $dbdict->ExecuteSQLArray($sqlarray);

verbose_msg('create table '.CmsLayoutTemplateCategory::TABLENAME);
$flds = "
         id I KEY AUTO,
         name C(100) NOTNULL,
         description X,
         item_order X,
         modified I";
$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX.CmsLayoutTemplateCategory::TABLENAME, $flds, $taboptarray);
$return = $dbdict->ExecuteSQLArray($sqlarray);
$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_layout_tpl_cat_1', CMS_DB_PREFIX.CmsLayoutTemplateCategory::TABLENAME,
                                    'name',array('UNIQUE'));
$return = $dbdict->ExecuteSQLArray($sqlarray);

verbose_msg('create table '.CmsLayoutTemplate::TABLENAME);
$flds = "
         id I KEY AUTO,
         name C(100) NOTNULL,
         content X2,
         description X,
         type_id I NOTNULL,
         type_dflt I1,
         category_id I,
         owner_id I NOTNULL,
         created I,
         modified I";
$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX.CmsLayoutTemplate::TABLENAME, $flds, $taboptarray);
$return = $dbdict->ExecuteSQLArray($sqlarray);

$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_layout_tpl_1', CMS_DB_PREFIX.CmsLayoutTemplate::TABLENAME, 'name',array('UNIQUE'));
$return = $dbdict->ExecuteSQLArray($sqlarray);

$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_layout_tpl_2', CMS_DB_PREFIX.CmsLayoutTemplate::TABLENAME, 'type_id,type_dflt');
$return = $dbdict->ExecuteSQLArray($sqlarray);

verbose_msg('create table '.CmsLayoutStylesheet::TABLENAME);
$flds = "
         id I KEY AUTO,
         name C(100) NOTNULL,
         content X2,
         description X,
  	     media_type C(255),
	     media_query X,
         created I,
         modified I";
$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX.CmsLayoutStylesheet::TABLENAME, $flds, $taboptarray);
$return = $dbdict->ExecuteSQLArray($sqlarray);
$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_layout_css_1',CMS_DB_PREFIX.CmsLayoutStylesheet::TABLENAME, 'name', array('UNIQUE'));
$return = $dbdict->ExecuteSQLArray($sqlarray);

verbose_msg('create table '.CmsLayoutTemplate::ADDUSERSTABLE);
$flds = "
         tpl_id I KEY,
         user_id I KEY
        ";
$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX.CmsLayoutTemplate::ADDUSERSTABLE, $flds, $taboptarray);
$return = $dbdict->ExecuteSQLArray($sqlarray);

verbose_msg('create table '.CmsLayoutCollection::TABLENAME);
$flds = "
         id   I KEY AUTO,
         name C(100) NOTNULL,
         description X,
         dflt I1,
         created I,
         modified I
        ";
$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX.CmsLayoutCollection::TABLENAME, $flds, $taboptarray);
$return = $dbdict->ExecuteSQLArray($sqlarray);
$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'idx_layout_dsn_1',CMS_DB_PREFIX.CmsLayoutCollection::TABLENAME, 'name', array('unique'));
$dbdict->ExecuteSQLArray($sqlarray);


verbose_msg('create table '.CmsLayoutCollection::TPLTABLE);
$flds = "
         design_id I KEY NOTNULL,
         tpl_id   I KEY NOTNULL
        ";
$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX.CmsLayoutCollection::TPLTABLE, $flds, $taboptarray);
$return = $dbdict->ExecuteSQLArray($sqlarray);
$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'index_dsnassoc1', CMS_DB_PREFIX.CmsLayoutCollection::TPLTABLE, 'tpl_id');
$return = $dbdict->ExecuteSQLArray($sqlarray);

verbose_msg('create table '.CmsLayoutCollection::CSSTABLE);
$flds = "
         design_id I KEY NOTNULL,
         css_id   I KEY NOTNULL,
         item_order I NOTNULL
        ";
$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX.CmsLayoutCollection::CSSTABLE, $flds, $taboptarray);
$return = $dbdict->ExecuteSQLArray($sqlarray);

verbose_msg('create table '.CmsLock::LOCK_TABLE);
$flds = "
         id I AUTO KEY NOTNULL,
         type C(20) NOTNULL,
         oid  I NOTNULL,
         uid  I NOTNULL,
         created I NOTNULL,
         modified I NOTNULL,
         lifetime I NOTNULL,
         expires  I NOTNULL
        ";
$sqlarray = $dbdict->CreateTableSQL(CMS_DB_PREFIX.CmsLock::LOCK_TABLE, $flds, $taboptarray);
$return = $dbdict->ExecuteSQLArray($sqlarray);

$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'index_locks1', CMS_DB_PREFIX."locks", 'type,oid', array('UNIQUE'));
$return = $dbdict->ExecuteSQLArray($sqlarray);

$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'index_locks2', CMS_DB_PREFIX."locks", 'expires');
$return = $dbdict->ExecuteSQLArray($sqlarray);

$sqlarray = $dbdict->CreateIndexSQL(CMS_DB_PREFIX.'index_locks3', CMS_DB_PREFIX."locks", 'uid');
$return = $dbdict->ExecuteSQLArray($sqlarray);

// create initial types.
$page_template_type = $gcb_template_type = null;
for( $tries = 0; $tries < 2; $tries++ ) {
    try {
        $page_template_type = CmsLayoutTemplateType::load(CmsLayoutTemplateType::CORE.'::page');
        $gcb_template_type = CmsLayoutTemplateType::load(CmsLayoutTemplateType::CORE.'::generic');
        break;
    }
    catch( \CmsDataNotFoundException $e ) {
        // we insert the records manually... because later versions of the template type
        // add different columns... and the save() method won't work.
        verbose_msg('create initial template types');

        $contents = \CmsTemplateResource::reset_page_type_defaults();
        $sql = 'INSERT INTO '.CMS_DB_PREFIX.\CmsLayoutTemplateType::TABLENAME.' (originator,name,has_dflt,dflt_contents,description,
                    lang_cb, dflt_content_cb, requires_contentblocks, owner, created, modified)
                VALUES (?,?,?,?,?,?,?,?,?,UNIX_TIMESTAMP(),UNIX_TIMESTAMP())';
        $dbr = $db->Execute( $sql, [ \CmsLayoutTemplateType::CORE, 'page', TRUE, $contents, null,
                                     serialize('CmsTemplateResource::page_type_lang_callback'),serialize('CmsTemplateResource::reset_page_type_default'), TRUE, null ] );
        $contents = null;
        $dbr = $db->Execute( $sql, [ \CmsLayoutTemplateType::CORE, 'generic', FALSE, null, null,
                                     serialize('CmsTemplateResource::generic_type_lang_callback'), null, FALSE, null ] );
    }
} // tries

    /*
    // if we got here.... the type does not exist.
    $page_template_type = new CmsLayoutTemplateType();
    $page_template_type->set_originator(CmsLayoutTemplateType::CORE);
    $page_template_type->set_name('page');
    $page_template_type->set_dflt_flag(TRUE);
    $page_template_type->set_lang_callback('CmsTemplateResource::page_type_lang_callback');
    $page_template_type->set_content_callback('CmsTemplateResource::reset_page_type_defaults');
    $page_template_type->reset_content_to_factory();
    $page_template_type->set_content_block_flag(TRUE);
    $page_template_type->save();

    $gcb_template_type = new CmsLayoutTemplateType();
    $gcb_template_type->set_originator(CmsLayoutTemplateType::CORE);
    $gcb_template_type->set_name('generic');
    $gcb_template_type->set_lang_callback('CmsTemplateResource::generic_type_lang_callback');
    $gcb_template_type->save();
    */
if( !is_object($page_template_type) || !is_object($gcb_template_type) ) {
    error_msg('The page template type and/or GCB template type could not be found or created');
    throw new \LogicException('This is bad');
}

$_fix_name = function($str) {
    if( CmsAdminUtils::is_valid_itemname($str) ) return $str;
    $orig = $str;
    $str = trim($str);
    if( !CmsAdminUtils::is_valid_itemname($str[0]) ) $str[0] = '_';
    for( $i = 1; $i < strlen($str); $i++ ) {
        if( !CmsAdminUtils::is_valid_itemname($str[$i]) ) $str[$i] = '_';
    }
    for( $i = 0; $i < 5; $i++ ) {
        $in = $str;
        $str = str_replace('__','_',$str);
        if( $in == $str ) break;
    }
    if( $str == '_' ) throw new \Exception('Invalid name '.$orig.' and cannot be corrected');
    return $str;
};

$_fix_css_name = function($str) {
    // stylesheet names cannot end with .css and must be unique
    if( !endswith($str,'.css') && CmsAdminUtils::is_valid_itemname($str) ) return $str;
    $orig = $str;
    $str = trim($str);
    if( !CmsAdminUtils::is_valid_itemname($str[0]) ) $str[0] = '_';
    for( $i = 1; $i < strlen($str); $i++ ) {
        if( !CmsAdminUtils::is_valid_itemname($str[$i]) ) $str[$i] = '_';
    }
    for( $i = 0; $i < 5; $i++ ) {
        $in = $str;
        $str = str_replace('__','_',$str);
        if( $in == $str ) break;
    }
    if( $str == '_' ) throw new \Exception('Invalid name '.$orig.' and cannot be corrected');
    return $str;
};

$fix_template_name = function($in) use (&$db,&$_fix_name) {
    // template names have to be unique and cannot end with .tpl
    if( endswith($in,'.tpl') ) $in = substr($in,0,-4);
    $in = $_fix_name($in);
    $name = CmsLayoutTemplate::generate_unique_name($in);
    if( $name != $in ) {
        error_msg('Template named '.$in.' conflicted with an existing template, new name is '.$name);
    }
    return $name;
};

// read gcb's and convert them to templates.
// note: we directly write the the CmsLayoutTemplate table instead of using the CmsLayoutTemplate API because
// the database structure changed between 2.0 and 2.1 (listable column) and the CmsLayoutTemplate class relies on a listable colum which may
// not yet exist.
verbose_msg('convert global content blocks to generic templates');
$query = 'SELECT * FROM '.CMS_DB_PREFIX.'htmlblobs';
$sql2 = 'INSERT INTO '.CMS_DB_PREFIX.CmsLayoutTemplate::TABLENAME.' (name,content,description,type_id,type_dflt,owner_id,created,modified) VALUES (?,?,?,?,0,?,UNIX_TIMESTAMP(),UNIX_TIMESTAMP())';
$gcblist = null;
$tmp = $db->GetArray($query);
if( is_array($tmp) && count($tmp) ) {
    // for each gcb, come up wit a new name and if the new name does not exist in the database, create a new template by that name.
    foreach( $tmp as $gcb ) {
        $new_name = $fix_template_name($gcb['htmlblob_name']);
        try {
            $template = CmsLayoutTemplate::load($new_name);
            // nothing here, template with this name exists.
        }
        catch( \CmsDataNotFoundException $e ) {
            $db->Execute($sql2,array($new_name,$gcb['html'],$gcb['description'],$gcb_template_type->get_id(),$gcb['owner']));
            $gcb['template_id'] = $db->Insert_ID();
            $gcblist[$gcb['htmlblob_id']] = $gcb;
        }
    }

    if( count($gcblist) ) {
        // process all of the additional owners, and sort them into an array of uids, one array for each gcb.
        $query = 'SELECT * FROM '.CMS_DB_PREFIX.'additional_htmlblob_users';
        $tmp = $db->GetArray($query);
        if( is_array($tmp) && count($tmp) ) {
            $users = array();
            foreach( $tmp as $row ) {
                $htmlblob_id = $row['htmlblob_id'];
                $uid = (int)$row['user_id'];
                if( $uid < 1 ) continue;
                if( !isset($gcblist[$htmlblob_id]) ) continue;
                if( $uid == $gcblist[$htmlblob_id]['owner'] ) continue;
                if( !isset($users[$htmlblob_id]) ) $users[$htmlblob_id] = array();
                $users[$htmlblob_id][] = (int)$row['user_id'];
            }
        }

        // now insert the additional editors directly into the database
        $sql3 = 'INSERT INTO '.CMS_DB_PREFIX.CmsLayoutTemplate::ADDUSERSTABLE.' (tpl_id, user_id) VALUES (?,?)';
        foreach( $gcblist as $htmlblob_id => $gcb ) {
            if( !isset($users[$htmlblob_id]) ) continue;
            foreach( $users[$htmlblob_id] as $add_uid ) {
                $db->Execute($sql3,array($gcb['template_id'],$add_uid));
            }
        }
    }
}
unset($gcblist,$tmp);

verbose_msg('dropping gcb related tables...');
$sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX.'additional_htmlblob_users_seq');
$dbdict->ExecuteSQLArray($sqlarray);
$sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX.'additional_htmlblob_users');
$dbdict->ExecuteSQLArray($sqlarray);
$sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX.'htmlblobs_seq');
$dbdict->ExecuteSQLArray($sqlarray);
$sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX.'htmlblobs');
$dbdict->ExecuteSQLArray($sqlarray);

verbose_msg('converting stylesheets');
$query = 'SELECT * FROM '.CMS_DB_PREFIX.'css';
$tmp = $db->GetArray($query);
if( is_array($tmp) && count($tmp) ) {
  $css_list = array();
  foreach( $tmp as $row ) {
      $new_name = $_fix_css_name($row['css_name']);
      if( $new_name != $row['css_name']) verbose_msg("Rename stylesheet ".$row['css_name']." to $new_name");
      try {
          $tmp = CmsLayoutStylesheet::load($new_name);
      }
      catch( \CmsLogicException $e ) {
          $css_id = $row['css_id'];
          $stylesheet = new CmsLayoutStylesheet();
          $stylesheet->set_name($new_name);
          $stylesheet->set_content($row['css_text']);
          $stylesheet->set_description('CMSMS Upgraded on '.$db->DbTimeStamp(time()));
          $stylesheet->set_media_types($row['media_type']);
          $stylesheet->set_media_query($row['media_query']);
          $stylesheet->save();

          $row['css_obj'] = $stylesheet;
          $csslist[$row['css_id']] = $row;
      }
  }
}
unset($tmp);

verbose_msg('converting page templates');
// todo: handle stylesheets that are orphaned
@ini_set('display_errors',1);
@error_reporting(E_ALL);


$tpl_query = 'SELECT * FROM '.CMS_DB_PREFIX.'templates';
$tpl_insert_query = 'INSERT INTO '.CMS_DB_PREFIX.CmsLayoutTemplate::TABLENAME.' (name,content,description,type_id,type_dflt,owner_id,created,modified) VALUES (?,?,?,?,?,?,UNIX_TIMESTAMP(),UNIX_TIMESTAMP())';
$css_assoc_query = 'SELECT * FROM '.CMS_DB_PREFIX.'css_assoc WHERE assoc_to_id = ? ORDER BY assoc_order ASC';
$tmp = $db->GetArray($tpl_query);
$template_list = array();
if( is_array($tmp) && count($tmp) ) {
    foreach( $tmp as $row ) {
        $row['template_name'] = $fix_template_name($row['template_name']);
        $is_default = (int) $row['default_template'];

        // create the design (one design per page template)
        $tpl_id = $row['template_id'];
        $design = new CmsLayoutCollection();
        $design->set_name($row['template_name']);
        $design->set_description('CMSMS Upgraded on '.$db->DbTimeStamp(time()));
        $design->set_default($is_default);
        $design->save(); // the design will now have an id.
        verbose_msg('created design '.$design->get_name());

        // create the template
        $db->Execute($tpl_insert_query,array($row['template_name'],$row['template_content'],'',$page_template_type->get_id(),
                                $is_default,1));
        $new_tpl_id = $db->Insert_ID();
        $design->add_template($new_tpl_id);
        $design->save(); // save the design again.

        $row['new_tpl_id'] = $new_tpl_id;
        $row['new_design_id'] = $design->get_id();
        $template_list[$tpl_id] = $row;
        verbose_msg('created template '.$row['template_name']);

        // get stylesheet(s) attached to this template
        // and associate them with the design.
        $associations = $db->GetArray($css_assoc_query,array($row['template_id']));
        if( is_array($associations) && count($associations) ) {
            foreach( $associations as $assoc ) {
                $css_id = $assoc['assoc_css_id'];
                if( !isset($csslist[$css_id]) ) continue;
                $design->add_stylesheet($csslist[$css_id]['css_obj']);
            }
            verbose_msg('associated '.count($associations).' stylesheets with the design');
            $design->save();
        }
    }
}
unset($tmp);

verbose_msg('adjusting pages');
$query = 'SELECT content_id,template_id,content_alias FROM '.CMS_DB_PREFIX.'content WHERE template_id > 0';
$uquery = 'UPDATE '.CMS_DB_PREFIX.'content SET template_id = ? WHERE content_id = ?';
$iquery = 'INSERT INTO '.CMS_DB_PREFIX.'content_props (content_id,type,prop_name,content,create_date,modified_date) VALUES (?,?,?,?,NOW(),NOW())';
$content_rows = $db->GetArray($query);
$contentops = ContentOperations::get_instance();
if( is_array($content_rows) && count($content_rows) ) {
    foreach( $content_rows as $row ) {
        if( $row['template_id'] < 1 ) continue;
        $content_id = $row['content_id'];

        $tpl_id = (int) $row['template_id'];
        if( !isset($template_list[$tpl_id]) ) {
            error_msg('ERROR: The page '.$row['content_alias'].' Refers to a template with id '.$tpl_id.' That was not found in the database');
            continue;
        }
        $tpl_row = $template_list[$tpl_id];
        if( !isset($tpl_row['new_tpl_id']) ) {
            error_msg("could not find map to new template for template $tpl_id on page $content_id");
            continue;
        }

        // because we create a new design on upgrade for each page template thre can be only one design
        $design_id = $tpl_row['new_design_id'];
        $tpl_id = $tpl_row['new_tpl_id'];

        $db->Execute($uquery,array($tpl_id,$content_id));
        $db->Execute($iquery,array($content_id,'string','design_id',$design_id));
        verbose_msg('adjusted page '.$row['content_alias']);
  }
}

verbose_msg('dropping old template tables');
$sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX.'templates');
$dbdict->ExecuteSQLArray($sqlarray);
$sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX.'templates_seq');
$dbdict->ExecuteSQLArray($sqlarray);
$sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX.'css_assoc');
$dbdict->ExecuteSQLArray($sqlarray);
$sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX.'css');
$dbdict->ExecuteSQLArray($sqlarray);
$sqlarray = $dbdict->DropTableSQL(CMS_DB_PREFIX.'css_seq');
$dbdict->ExecuteSQLArray($sqlarray);

verbose_msg('uninstalling theme manager');
$modops = ModuleOperations::get_instance();
$modops->UninstallModule('ThemeManager');

verbose_msg('upgrading cms_groups table');
$sqlarray = $dbdict->AddColumnSQL(CMS_DB_PREFIX.'groups','group_desc C(255)');
$dbdict->ExecuteSQLArray($sqlarray);

verbose_msg('Remove the CMSPrinting module from the database');
$query = 'DELETE FROM '.CMS_DB_PREFIX.'modules WHERE module_name = ?';
$db->Execute($query,array('CMSPrinting'));

verbose_msg('Creating print UDT');
$txt = <<<EOT
echo '<!-- print tag removed in CMS Made Simple 2.0.  -->';
EOT;
UserTagOperations::get_instance()->SetUserTag('print',$txt,'Stub function to replace the print plugin');

$sql = 'SELECT username FROM '.CMS_DB_PREFIX.'users WHERE user_id = 1';
$un = $db->GetOne($sql);
if( $un ) {
    // make sure that if we have a user with id=1 that this user is in the admin (gid=1) group
    // as 2.0 now does not magically check uid's just gid's for admin access.
    try {
        $sql = 'INSERT INTO '.CMS_DB_PREFIX.'user_groups (group_id,user_id) VALUES (1,1)';
        $db->Execute($sql);
    }
    catch( \Exception $e ) {
        // this can throw an exception, if the user is already in this group... let it.
    }
}

verbose_msg(ilang('reset_user_settings'));
$query = 'DELETE FROM '.CMS_DB_PREFIX.'userprefs WHERE preference = ?';
$db->Execute($query,array('admintheme'));
$db->Execute($query,array('collapse'));
$db->Execute($query,array('wysiwyg'));

verbose_msg(ilang('reset_site_preferences'));
$query = 'DELETE FROM '.CMS_DB_PREFIX.'WHERE sitepref_name = ?';
$db->Execute($query,array('logintheme'));

verbose_msg(ilang('queue_for_upgrade','CMSMailer'));
\ModuleOperations::get_instance()->QueueForInstall('CMSMailer');

verbose_msg(ilang('upgrading_schema',200));
$query = 'UPDATE '.CMS_DB_PREFIX.'version SET version = 200';
$db->Execute($query);

status_msg('done upgrades for 2.0');
?>

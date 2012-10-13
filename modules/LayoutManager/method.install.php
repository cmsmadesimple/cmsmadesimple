<?php // -*- mode:php; tab-width:2; indent-tabs-mode:t; c-basic-offset:2; -*-
#-------------------------------------------------------------------------
# Module: AdminSearch - A CMSMS addon module to provide template management.
# (c) 2012 by Robert Campbell <calguy1000@cmsmadesimple.org>
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------
if( !isset($gCms) ) exit;

$dict = NewDataDictionary($db);
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$db_prefix = cms_db_prefix();

$flds = "
         id I KEY AUTO,
         originator C(50) NOT NULL,
         name C(50) NOT NULL,
         has_dflt I1,
         dflt_contents X2,
         description X,
         lang_cb     C(255),
         dflt_content_cb C(255),
         owner   I,
         created I,
         modified I";
$sqlarray = $dict->CreateTableSQL($db_prefix.CmsLayoutTemplateType::TABLENAME, $flds, $taboptarray);
$return = $dict->ExecuteSQLArray($sqlarray);
// todo: indexes here.

$flds = "
         id I KEY AUTO,
         name C(50) NOT NULL,
         description X,
         item_order X,
         modified I";
$sqlarray = $dict->CreateTableSQL($db_prefix.CmsLayoutTemplateCategory::TABLENAME, $flds, $taboptarray);
$return = $dict->ExecuteSQLArray($sqlarray);

$flds = "
         id I KEY AUTO,
         name C(50) NOT NULL,
         content X2,
         description X,
         type_id I,
         type_dflt I1,
         category_id I,
         owner_id I,
         created I,
         modified I";
$sqlarray = $dict->CreateTableSQL($db_prefix.CmsLayoutTemplate::TABLENAME, $flds, $taboptarray);
$return = $dict->ExecuteSQLArray($sqlarray);

$flds = "
         tpl_id I KEY,
         user_id I KEY
        ";
$sqlarray = $dict->CreateTableSQL($db_prefix.CmsLayoutTemplate::ADDUSERSTABLE, $flds, $taboptarray);
$return = $dict->ExecuteSQLArray($sqlarray);


$flds = "
         id   I KEY AUTO,
         name C(50) NOT NULL,
         description X,
         created I,
         modified I
        ";
$sqlarray = $dict->CreateTableSQL($db_prefix.CmsLayoutTheme::TABLENAME, $flds, $taboptarray);
$return = $dict->ExecuteSQLArray($sqlarray);


$flds = "
         theme_id I KEY NOT NULL,
         tpl_id   I KEY NOT NULL
        ";
$sqlarray = $dict->CreateTableSQL($db_prefix.CmsLayoutTheme::TPLTABLE, $flds, $taboptarray);
$return = $dict->ExecuteSQLArray($sqlarray);

$flds = "
         theme_id I KEY NOT NULL,
         css_id   I KEY NOT NULL,
         item_order I NOT NULL
        ";
$sqlarray = $dict->CreateTableSQL($db_prefix.CmsLayoutTheme::CSSTABLE, $flds, $taboptarray);
$return = $dict->ExecuteSQLArray($sqlarray);

//
// create some sample data.
//
$type = new CmsLayoutTemplateType();
$type->set_originator('__CORE__');
$type->set_name('Generic');
$type->set_dflt_contents('{* Generic Template *}');
$type->set_lang_callback('LayoutManager::lang_callbackk');
$type->save();

$type = new CmsLayoutTemplateType();
$type->set_originator('__CORE__');
$type->set_name('Page');
$type->set_dflt_flag(true);
$type->set_lang_callback('LayoutManager::lang_callbackk');
$type->set_content_callback('LayoutManager::reset_page_template');
$type->reset_content_to_factory();
$type->save();

$category = new CmsLayoutTemplateCategory();
$category->set_name('Sample Category');
$category->set_description('This is a Sample Category');
$category->save();

$theme = new CmsLayoutTheme;
$theme->set_name('Sample');
$theme->set_description('Sample Theme Description');
// no attached stylesheets or templates yet.
$theme->save();

$this->CreatePermission('Manage Templates','Manage Templates');
$this->CreatePermission('Add Templates','Add Templates');

#
# EOF
#
?>
<?php
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
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
#$Id$
$CMS_ADMIN_PAGE=1;
$LOAD_ALL_MODULES=1;

require_once("../include.php");
check_login();
$smarty = cms_smarty();
include_once("header.php");

//
// Initialize
//
$modulename = '';
if( isset($_GET['module']) )
  {
    $modulename = trim($_GET['module']);
  }
if( empty($modulename) )
  {
    redirect('listmodules.php');
    return;
  }

$module = CmsModuleOperations::get_module($modulename);
if( is_null($module) )
  {
    redirect('listmodules.php');
    return;
  }

$module_dir2 = cms_join_path(CmsConfig::get('root_path'),'tmp','cache');
$tmpdir = cms_join_path(CmsConfig::get('root_path'),'tmp','cache',$module->get_name());
$destdir = cms_join_path(CmsConfig::get('root_path'),'tmp');
$fname = $module->get_name().'-'.$module->get_version().'.cmsmod';
$destfn = cms_join_path($destdir,$fname);
$module_dir = cms_join_path(CmsConfig::get('root_path'),'modules',$module->get_name());
if( !is_dir($module_dir) )
  {
    redirect('listmodules.php');
    return;
  }


//
// Copy the module to a tmp directory
//
if( is_dir($tmpdir) )
  {
    recursive_delete($tmpdir);
  }
@mkdir($tmpdir);
copyr($module_dir,$tmpdir);

//
// Generate metadata files
//
$about = $module->get_about();
@file_put_contents(cms_join_path($tmpdir,'__about__.txt'),$about);
$help = $module->get_help();
@file_put_contents(cms_join_path($tmpdir,'__help__.txt'),$about);
$depends = implode('::DEPEND::',$module->get_dependencies());
@file_put_contents(cms_join_path($tmpdir,'__depends__.txt'),$depends);

//
// Create the archive
// 
if( file_exists($destfn) )
  {
    @unlink($destfn);
  }
$archive = new CmsArchive($destfn);
$archive->set_basedir($module_dir2); // directory above the module files
$archive->set_recursive();
$archive->set_exclude_patterns(array('\.git', '\.svn' , '^CVS$' , '^\#.*\#$' , '~$', '\.bak$' ));
$archive->add_files(array($module->get_name()));
$archive->create();

//
// Send the file
//

$handlers = ob_list_handlers(); 
for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) { ob_end_clean(); }
header('Content-Description: File Transfer');
header('Content-Type: application/force-download');
header('Content-Disposition: attachment; filename='.$fname);
echo @file_get_contents($destfn);

//
// Cleanup
//
#recursive_delete($tmpdir);
#@unlink($destfn);
exit();

#
#
# vim:ts=4 sw=4 noet
?>

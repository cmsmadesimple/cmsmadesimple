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
#$Id$

/**
 * @package CMS
 * @ignore
 */

/**
 * A function for auto-loading classes.
 *
 * @since 1.7
 * @internal
 * @ignore
 * @param string A class name
 * @return boolean
 */
function cms_autoloader($classname)
{
    static $gCms;
    if( !$gCms ) $gCms = cmsms(); // for compatibility

    if( startswith($classname,'CMSMS\\') ) {
        $path = str_replace('\\','/',substr($classname,6));
        $classname = basename($path);
        $path = dirname($path);
        $filenames = array("class.{$classname}.php","interface.{$classname}.php","trait.{$classname}.php");
        foreach( $filenames as $test ) {
            $fn = cms_join_path(CMS_ROOT_PATH,'lib','classes',$path,$test);
            if( is_file($fn) ) {
                require_once($fn);
                return;
            }
        }
    }

    // standard classes
    $fn = cms_join_path(CMS_ROOT_PATH,'lib','classes',"class.{$classname}.php");
    if( is_file($fn) ) {
        require_once($fn);
        return;
    }

    // standard internal classes
    $fn = cms_join_path(CMS_ROOT_PATH,'lib','classes','internal',"class.{$classname}.php");
    if( is_file($fn) ) {
        require_once($fn);
        return;
    }

    // lowercase classes
    $lowercase = strtolower($classname);
    $fn = cms_join_path(CMS_ROOT_PATH,'lib','classes',"class.{$lowercase}.inc.php");
    if( is_file($fn) && $classname != 'Content' ) {
        require_once($fn);
        return;
    }

    // lowercase internal classes
    $lowercase = strtolower($classname);
    $fn = cms_join_path(CMS_ROOT_PATH,'lib','classes','internal',"class.{$lowercase}.inc.php");
    if( is_file($fn) && $classname != 'Content' ) {
        require_once($fn);
        return;
    }

    // standard interfaces
    $fn = cms_join_path(CMS_ROOT_PATH,'lib','classes',"interface.{$classname}.php");
    if( is_file($fn) ) {
        require_once($fn);
        return;
    }

    // internal interfaces
    $fn = cms_join_path(CMS_ROOT_PATH,'lib','classes','internal',"interface.{$classname}.php");
    if( is_file($fn) ) {
        require_once($fn);
        return;
    }

    // standard content types
    $fn = cms_join_path(CMS_ROOT_PATH,'lib','classes','contenttypes',"{$classname}.inc.php");
    if( is_file($fn) ) {
        require_once($fn);
        return;
    }

    // standard tasks
    if( endswith($classname,'Task') ) {
        $class = substr($classname,0,-4);
        $fn = CMS_ROOT_PATH."/lib/tasks/class.{$class}.task.php";
        if( is_file($fn) ) {
            require_once($fn);
            return;
        }
    }

    // if requesting a module..
    // note, if force loading we include the file.
    // if not forceloading.  we load the module.
    $modops = $gCms->GetModuleOperations();
    if( ($fn = $modops->get_module_filename( $classname )) ) {
        if( is_file( $fn ) ) {
            require_once($fn);
            return;
        }
    }

    // loaded module classes.
    $modules = $modops->GetLoadedModules();
    if( is_null($modules) ) return;
    $list = array_keys($modules);
    $tmp = ltrim(str_replace('\\','/',$classname),'/');
    $class_base = basename($tmp);
    $dirname = dirname($tmp);
    if( is_array($list) && count($list) ) {
        if( in_array($dirname,$list) ) {
            $modpath = $modops->get_module_path( $dirname );
            $fn = "$modpath/lib/class.$class_base.php";
            if( is_file( $fn ) ) {
                require_once($fn);
                return;
            }

        }

        // handle \ModuleName\<path>\Class
        $tmp = ltrim(str_replace('\\','/',$classname),'/');
        $p1 = strpos($tmp,'/');
        if( $p1 !== FALSE ) {
            $pos1 = strpos($tmp,'/');
            $modname = substr($tmp,0,strpos($tmp,'/'));
            if( in_array($modname,$list) ) {
                $modpath = $modops->get_module_path( $modname );
                $subpath = dirname(substr($tmp,$pos1+1));
                $class = basename($tmp);
                $fn = "$modpath/lib/$subpath/class.$class.php";
                if( is_file($fn) ) {
                    require_once($fn);
                    return;
                }
            }
        }

        // handle class Foo (search in loaded modules)
        foreach( $list as $modname ) {
            $modpath = $modops->get_module_path( $modname );
            $fn = "$modpath/lib/class.$classname.php";
            if( is_file($fn) ) {
                require_once($fn);
                return;
            }
        }
    }
}

spl_autoload_register('cms_autoloader');

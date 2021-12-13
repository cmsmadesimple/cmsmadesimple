<?php
#CMS - CMS Made Simple
#(c)2004-2012 by Ted Kulp (wishy@users.sf.net)
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
#$Id: content.functions.php 6863 2011-01-18 02:34:48Z calguy1000 $

/**
 * @package CMS
 */

/**
 * A simple class for handling css code as a resource.
 *
 * @package CMS
 * @author Robert Campbell
 * @internal
 * @ignore
 * @copyright Copyright (c) 2012, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 1.12
 */
class CmsStylesheetResource extends CMS_Fixed_Resource_Custom
{
    protected function fetch($name,&$source,&$mtime)
    {
        // clean up the input
        $name = trim($name);
        if( !$name ) return;

        // if called via function.cms_stylesheet, then this stylesheet should be loaded.
        $obj = CmsLayoutStylesheet::load($name);

        // by now everything should be in memory in the CmsLayoutStylesheet internal cache's
        // put it all together in the order specified.
        $text = '/* cmsms stylesheet: '.$obj->get_name().' modified: '.\locale_ftime('%x %X',$obj->get_modified()).' */'."\n";
        $text .= $obj->get_content();
        if( !endswith($text,"\n") ) $text .= "\n";

        $mtime = $obj->get_modified();
        $source = $text;
    }

} // end of class

#
# EOF
#
?>

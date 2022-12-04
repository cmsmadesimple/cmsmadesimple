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
 * This file contains a smarty resource handler for content pages.
 *
 * @internal
 * @ignore
 * @package CMS
 * @copyright Copyright (c) 2012, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 1.11
 */

/**
 * A simple class for handling the content smarty resource.
 *
 * @package CMS
 * @author Robert Campbell
 * @internal
 * @ignore
 * @copyright Copyright (c) 2012, Robert Campbell <calguy1000@cmsmadesimple.org>
 * @since 1.11
 */
class CMSContentTemplateResource extends CMS_Fixed_Resource_Custom
{
    protected function fetch($name,&$source,&$mtime)
    {
        $gCms = CmsApp::get_instance();
        $contentobj = $gCms->get_content_object();

        if (!is_object($contentobj)) {
            // We've a custom error message...  return it here
            header("HTTP/1.0 404 Not Found");
            header("Status: 404 Not Found");
            $source = null;
            if ($name == 'content_en') $source = cms_siteprefs::get('custom404');
            $mtime = time();
            $source = trim($source);
            return;
        }
        else if( isset($_SESSION['__cms_preview_']) && $contentobj->Id() == __CMS_PREVIEW_PAGE__ ) {
            $contentobj =& $_SESSION['__cms_preview__'];
            $source = $contentobj->Show($name);
            $mtime = time();
            $source = preg_replace("/\{\/?php\}/", "", $source);
            $source = trim($source);
            return;
        }
        else if (isset($contentobj) && $contentobj !== FALSE) {
            $mtime = $contentobj->GetModifiedDate();
            $source = $contentobj->Show($name);
            $source = preg_replace('/\{\/?php\}/', '', $source);
            if( !$contentobj->Cachable() ) $mtime = time();
            $source = trim($source);
            return;
        }
        $source = null;
        $mtime = null;
        return;
    }
} // end of class


#
# EOF
#
?>
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
 * Generic smarty security policy.
 *
 * @since		1.11
 * @package		CMS
 * @internal
 * @ignore
 */
final class CMSSmartySecurityPolicy extends Smarty_Security
{
    public function __construct($smarty)
    {
        parent::__construct($smarty);
        $this->php_handling = Smarty::PHP_REMOVE;
        $this->secure_dir = null;
        $this->php_modifiers = [];
        $this->streams = null;
        $this->allow_constants = false;
        $this->allow_php_tag = FALSE;
        $gCms = CmsApp::get_instance();
        if($gCms->is_frontend_request() ) {
            $this->static_classes = array(); // allow all static classes
            $this->php_functions = array(); // allow any php functions
            $config = $gCms->GetConfig();
            if( !$config['permissive_smarty'] ) {
                $this->static_classes = null;
                // this should allow most stuff that does modification to data or formatting.
                // i.e: string searches, array searches, string comparison, sorting, etc.
                $this->php_functions = ['isset', 'implode','explode','empty','count', 'sizeof','in_array', 'is_array','time','lang',
                                        'date','strftime','strtotime',
                                        'str_replace','is_string','strpos','substr','strtolower','strtoupper','strcmp','strcasecmp','strlen',
                                        'array_search','array_reverse', 'array_flip','shuffle','array_rand','sort','ksort','asort',
					'array_sum', 'array_diff', 'array_combine',
                                        'nl2br','file_exists', 'is_object', 'is_file','is_dir','print_r','var_dump',
                                        'debug_display','startswith', 'endswith', 'urlencode','json_encode','json_decode','is_email',
                                        'htmlspecialchars','htmlspecialchars_decode','cms_html_entity_decode','cms_to_bool', 'min', 'max' ];
            }
        }
        else {
            $this->php_functions = [];
            $this->static_classes = [];
            $this->allow_constants = true;
        }
    }
} // end of class

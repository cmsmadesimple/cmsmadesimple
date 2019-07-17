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

namespace CMSMS\internal;

// manages the current theme.... and only the current theme.
// used by {cms_set_theme} and {cms_theme_url} and {cms_theme_path}
class current_theme_manager
{
    private $theme;

    public function set_theme(string $theme)
    {
        if( $this->theme ) return;
        $theme = trim($theme);
        $path = CMS_ASSETS_PATH."/themes/$theme";
        if( !is_dir($path) ) throw new \InvalidArgumentException("cannot set invalid theme $theme");
        $this->theme = $theme;
    }

    public function get_theme()
    {
        return $this->theme;
    }

    public function get_theme_path(string $theme = null) : string
    {
	if( !$theme ) $theme = $this->theme;
        if( !$theme ) throw new \LogicException('A theme has not been set');
        return CMS_ASSETS_PATH."/themes/".$theme;
    }

    public function get_theme_url(string $theme = null) : string
    {
	if( !$theme ) $theme = $this->theme;
        if( !$theme ) throw new \LogicException('A theme has not been set');
        return CMS_ASSETS_URL."/themes/".$theme;
    }
} // class

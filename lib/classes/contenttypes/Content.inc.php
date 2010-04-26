<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
# CMS - CMS Made Simple
# (c)2004-2009 by Ted Kulp (ted@cmsmadesimple.org)
# This project's homepage is: http://cmsmadesimple.org
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# BUT withOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

class Content extends CmsContentBase
{
    function __construct()
    {
		parent::__construct();

		#Turn on preview
		$this->set_previewable(true);

	}

    function is_copyable()
    {
        return TRUE;
    }

	function is_previewable()
	{
		return TRUE;
	}

	function is_viewable()
	{
		return TRUE;
	}

    function friendly_name()
    {
      return lang('contenttype_content');
    }


	public function get_type()
	{
	  return 'content';
	}


    function Show($param = 'content_en')
    {
	  // check for additional content blocks
	  return $this->get_property_value($param);
    }


    function is_default_possible()
    {
	  return TRUE;
    }


	protected function validate()
	{
		parent::validate();
		if( $this->template_id() < 0 )
			{
				$this->add_validation_error(lang('nofieldgiven',array(lang('template'))));
			}

		$tmp = $this->get_property_value('content_en');
		if( $tmp == '' )
			{
				$this->add_validation_error(lang('nofieldgiven',array(lang('content'))));
			}
	}

} // end of class

# vim:ts=4 sw=4 noet
?>

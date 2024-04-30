<?php
#-------------------------------------------------------------------------
# Module: DesignManager - A CMSMS addon module to provide template management.
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

final class DesignManager extends CMSModule
{
    function GetFriendlyName()  { return $this->Lang('friendlyname');  }
    function GetVersion()  { return '1.1.11'; }
    function MinimumCMSVersion()  { return '2.1'; }
    function LazyLoadAdmin() { return TRUE; }
    function LazyLoadFrontend() { return TRUE; }
    function IsPluginModule() { return FALSE; }
    function GetAuthor() { return 'Calguy1000'; }
    function GetAuthorEmail() { return 'calguy1000@cmsmadesimple.org'; }
    function HasAdmin() { return true; }
    function GetAdminSection() { return 'layout'; }
    function AllowAutoInstall() { return TRUE; }
    function GetHelp() { return CmsLangOperations::lang_from_realm('help','help_designmanager_help'); }
    function GetChangeLog() { return file_get_contents(__DIR__.'/changelog.inc'); }
    function GetAdminDescription() { return $this->Lang('moddescription'); }
    function InstallPostMessage() { return $this->Lang('postinstall'); }
    function UninstallPostMessage() { return $this->Lang('postuninstall'); }

    function VisibleToAdminUser()
    {
        if( $this->CheckPermission('Add Templates') ||
            $this->CheckPermission('Modify Templates') ||
            $this->CheckPermission('Manage Stylesheets') ||
            $this->CheckPermission('Manage Designs') ||
            !empty(CmsLayoutTemplate::get_editable_templates(get_userid())) ) return TRUE;
        return FALSE;
    }

    public function DoAction($name,$id,$params,$returnid='')
    {
        $smarty = cmsms()->GetSmarty();
        $smarty->assign('mod',$this);
        return parent::DoAction($name,$id,$params,$returnid);
    }

    public function GetAdminMenuItems()
    {
        $out = array();
        if( $this->VisibleToAdminUser() ) $out[] = CmsAdminMenuItem::from_module($this);

        if( $this->CheckPermission('Modify Site Preferences') ) {
            $obj = new CmsAdminMenuItem();
            $obj->module = $this->GetName();
            $obj->section = 'siteadmin';
            $obj->title = $this->Lang('title_designmanager_settings');
            $obj->description = $this->Lang('desc_designmanager_settings');
            $obj->action = 'admin_settings';
            $out[] = $obj;
        }
        return $out;
    }

    public function GetEventHelp( $eventname )
    {
        return lang('event_help_'.$eventname);
    }

    public function GetEventDescription( $eventname )
    {
        return lang('event_desc_'.$eventname);
    }

	/**
	 * A module method for handling module response with ajax actions, returning a JSON encoded response.
	 * @param  string $status The status of returned response, in example error, success, warning, info
	 * @param  string $message The message of returned response
	 * @param  mixed $data A string or array of response data
	 * @return string Returns a string containing the JSON representation of provided response data
	 */
	public function GetJSONResponse($status, $message, $data = null)
	{

		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {

			$handlers = ob_list_handlers();
			for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) { ob_end_clean(); }

			header('Content-type:application/json; charset=utf-8');

			if ($data) {
				$json = json_encode(array('status' => $status, 'message' => $message, 'data' => $data));
			} else {
				$json = json_encode(array('status' => $status, 'message' => $message));
			}

			echo $json;
			exit();
		}

		return false;
	}
} // class

#
# EOF
#
?>

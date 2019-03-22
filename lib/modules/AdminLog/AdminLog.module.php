<?php
#-------------------------------------------------------------------------
# Module: AdminLog - A CMSMS addon module to provide functionality for working with
#   The CMSM Audit Log
# (c) 2017 by Robert Campbell <calguy1000@cmsmadesimple.org>
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
# This projects homepage is: http://www.cmsmadesimple.org
#-------------------------------------------------------------------------
#-------------------------------------------------------------------------
# BEGIN_LICENSE
#-------------------------------------------------------------------------
# This file is part of AdminLog
# AdminLog is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# AdminLog is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#-------------------------------------------------------------------------
# END_LICENSE
#-------------------------------------------------------------------------

require_once(__DIR__.'/lib/class.storage.php');
require_once(__DIR__.'/lib/class.auditor.php');
require_once(__DIR__.'/lib/class.event.php');

final class AdminLog extends \CMSModule
{

    protected $storage;

    protected $auditor;

    public function InitializeAdmin()
    {
        parent::InitializeAdmin();

        \CMSMS\HookManager::add_hook('localizeperm',function($perm_source,$perm_name) {
                if( $perm_source != 'AdminLog' ) return;
                $key = 'perm_'.str_replace(' ','_',$perm_name);
                return $this->Lang($key);
        });
        \CMSMS\HookManager::add_hook('getperminfo',function($perm_source,$perm_name) {
                if( $perm_source != 'AdminLog' ) return;
                $key = 'permdesc_'.str_replace(' ','_',$perm_name);
                return $this->Lang($key);
        });
    }

    public function SetParameters()
    {
        $this->storage = new \AdminLog\storage( $this );
        $this->auditor = new \AdminLog\auditor( $this, $this->storage );

        try {
            \CMSMS\AuditManager::set_auditor( $this->auditor );
        }
        catch( \Exception $e ) {
            // ignore any error.
        }
    }

    public function GetFriendlyName()
    {
        return $this->Lang('friendlyname');
    }

    public function GetVersion()
    {
        return '1.0';
    }

    public function MinimumCMSVersion()
    {
        return '2.2.900';
    }

    public function GetHelp()
    {
        return $this->Lang('help');
    }

    public function IsPluginModule()
    {
        return FALSE;
    }

    public function HasAdmin()
    {
        return TRUE;
    }

    public function GetAdminSection()
    {
        return 'siteadmin';
    }

    public function VisibleToAdminUser()
    {
        return $this->CheckPermission('Modify Site Preferences');
    }

    public function HasCapability($capability, $params = array())
    {
        if( $capability == \CmsCoreCapabilities::TASKS ) return TRUE;
        if( $capability == 'clicommands' ) return TRUE;
    }

    public function get_tasks()
    {
        $out = [];
        $out[] = new \AdminLog\AutoPruneLogTask();
        $out[] = new \AdminLog\ReduceLogTask();
        return $out;
    }

    public function get_cli_commands( $app )
    {
        if( ! $app instanceof \CMSMS\CLI\App ) throw new \LogicException(__METHOD__.' Called from outside of cmscli');
        if( !class_exists('\\CMSMS\\CLI\\GetOptExt\\Command') ) throw new \LogicException(__METHOD__.' Called from outside of cmscli');

        $out = [];
        $out[] = new \AdminLog\ClearLogCommand( $app );
        return $out;
    }
} // end of class

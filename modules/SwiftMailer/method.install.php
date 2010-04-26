<?php
#-------------------------------------------------------------------------
# Module: SwiftMailer - a simple wrapper around swift
# Version: 1.0, Ted Kulp <ted@cmsmadesimple.org>
#
#-------------------------------------------------------------------------
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

if (!isset($gCms)) exit;

$this->Preference->set('mailer', 'smtp');
$this->Preference->set('host', 'localhost');
$this->Preference->set('port', 25 );
$this->Preference->set('from', 'root@localhost');
$this->Preference->set('fromuser', 'CMS Administrator');
$this->Preference->set('sendmail', '/usr/sbin/sendmail');
$this->Preference->set('timeout', 1000);
$this->Preference->set('smtpauth', 0);
$this->Preference->set('username', '');
$this->Preference->set('password', '');

?>
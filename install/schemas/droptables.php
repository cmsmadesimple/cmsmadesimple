<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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

CmsInstallOperations::drop_table($db, 'additional_users');
CmsInstallOperations::drop_table($db, 'admin_bookmarks');
CmsInstallOperations::drop_table($db, 'adminlog');
CmsInstallOperations::drop_table($db, 'admin_recent_pages');
CmsInstallOperations::drop_table($db, 'attribute_defns');
CmsInstallOperations::drop_table($db, 'attributes');
CmsInstallOperations::drop_table($db, 'content');
CmsInstallOperations::drop_table($db, 'content_props');
CmsInstallOperations::drop_table($db, 'crossref');
CmsInstallOperations::drop_table($db, 'event_handlers');
CmsInstallOperations::drop_table($db, 'events');
CmsInstallOperations::drop_table($db, 'group_perms');
CmsInstallOperations::drop_table($db, 'group_permissions');
CmsInstallOperations::drop_table($db, 'groups');
CmsInstallOperations::drop_table($db, 'htmlblobs');
CmsInstallOperations::drop_table($db, 'additional_htmlblob_users');
CmsInstallOperations::drop_table($db, 'modules');
CmsInstallOperations::drop_table($db, 'module_deps');
CmsInstallOperations::drop_table($db, 'module_templates');
CmsInstallOperations::drop_table($db, 'multilanguage');
CmsInstallOperations::drop_table($db, 'permissions');
CmsInstallOperations::drop_table($db, 'permission_defns');
CmsInstallOperations::drop_table($db, 'serialized_versions');
CmsInstallOperations::drop_table($db, 'siteprefs');
CmsInstallOperations::drop_table($db, 'stylesheets');
CmsInstallOperations::drop_table($db, 'stylesheet_template_assoc');
CmsInstallOperations::drop_table($db, 'templates');
CmsInstallOperations::drop_table($db, 'user_groups');
CmsInstallOperations::drop_table($db, 'userprefs');
CmsInstallOperations::drop_table($db, 'users');
CmsInstallOperations::drop_table($db, 'userplugins');
CmsInstallOperations::drop_table($db, 'version');

# vim:ts=4 sw=4 noet
?>

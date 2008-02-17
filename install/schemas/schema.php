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

CmsInstallOperations::create_table($db, 'additional_users', "
	id I KEY AUTO,
	user_id I,
	page_id I,
	content_id I
");

CmsInstallOperations::create_table($db, 'admin_bookmarks', "
	id I KEY AUTO,
	user_id I,
	title C(255),
	url C(255)
");
CmsInstallOperations::create_index($db, 'admin_bookmarks', 'user_id', 'user_id');

CmsInstallOperations::create_table($db, 'adminlog', "
	timestamp I,
	user_id I,
	username C(25),
	item_id I,
	item_name C(50),
	action C(255)
");

CmsInstallOperations::create_table($db, 'admin_recent_pages', "
	id I KEY AUTO,
	user_id I,
	title C(255),
	url C(255),
	access_time T
");

CmsInstallOperations::create_table($db, 'content', "
	id I KEY AUTO,
	content_name C(255),
	type C(25),
	owner_id I,
	parent_id I,
	template_id I,
	item_order I,
	lft I,
	rgt I,
	hierarchy C(255),
	default_content I1,
	menu_text C(255),
	content_alias C(255),
	show_in_menu I1,
	collapsed I1,
	markup C(25),
	active I1,
	cachable I1,
	id_hierarchy C(255),
	hierarchy_path X,
	prop_names X,
	metadata X,
	titleattribute C(255),
	tabindex C(10),
	accesskey C(5),
	last_modified_by I,
	create_date T,
	modified_date T
");
CmsInstallOperations::create_index($db, 'content', 'alias_and_active', 'content_alias,active');
CmsInstallOperations::create_index($db, 'content', 'default_content', 'default_content');
CmsInstallOperations::create_index($db, 'content', 'parent_id', 'parent_id');

CmsInstallOperations::create_table($db, 'content_props', "
	id I KEY AUTO,
	content_id I,
	type C(25),
	prop_name C(255),
	language C(50),
	content XL,
	create_date T,
	modified_date T
");
CmsInstallOperations::create_index($db, 'content_props', 'content_id', 'content_id');

CmsInstallOperations::create_table($db, 'crossref', "
	child_type C(100),
	child_id I,
	parent_type C(100),
	parent_id I,
	create_date T,
	modified_date T
");

CmsInstallOperations::create_index($db, 'crossref', 'child_type_and_id', 'child_type,child_id');
CmsInstallOperations::create_index($db, 'crossref', 'parent_type_and_id', 'parent_type,parent_id');

CmsInstallOperations::create_table($db, 'event_handlers', "
	id I KEY AUTO,
	event_id I,
	tag_name C(255),
	module_name C(100),
	removable I,
	handler_order I
");

CmsInstallOperations::create_table($db, 'events', "
	id I KEY AUTO,
	originator C(200) NOTNULL,
	event_name C(200) NOTNULL
");
CmsInstallOperations::create_index($db, 'events', 'originator', 'originator');
CmsInstallOperations::create_index($db, 'events', 'event_name', 'event_name');

CmsInstallOperations::create_table($db, 'group_perms', "
	id I KEY AUTO,
	group_id I,
	permission_id I,
	create_date T,
	modified_date T
");
CmsInstallOperations::create_index($db, 'group_perms', 'group_and_permission', 'group_id,permission_id');

CmsInstallOperations::create_table($db, 'group_permissions', "
	id I KEY AUTO,
	permission_defn_id I,
	group_id I,
	object_id I,
	has_access I1 DEFAULT 1
");

CmsInstallOperations::create_table($db, 'groups', "
	id I KEY AUTO,
	group_name C(25),
	active I1,
	create_date T,
	modified_date T
");

CmsInstallOperations::create_table($db, 'htmlblobs', "
	id I KEY AUTO,
	htmlblob_name C(255),
	html XL,
	owner I,
	version I,
	create_date T,
	modified_date T
");
CmsInstallOperations::create_index($db, 'htmlblobs', 'htmlblob_name', 'htmlblob_name');

CmsInstallOperations::create_table($db, 'additional_htmlblob_users', "
	id I KEY AUTO,
	user_id I,
	htmlblob_id I
");

CmsInstallOperations::create_table($db, 'modules', "
	module_name C(100),
	status C(50),
	version C(50),
	admin_only I1 DEFAULT 0,
	active I1
");
CmsInstallOperations::create_index($db, 'modules', 'module_name', 'module_name');

CmsInstallOperations::create_table($db, 'module_deps', "
	parent_module C(100),
	child_module C(100),
	minimum_version C(25),
	create_date T,
	modified_date T
");

CmsInstallOperations::create_table($db, 'module_templates', "
	id I KEY AUTO,
	module_name C(100) NOTNULL,
	template_type C(100) NOTNULL default '',
	template_name C(150) NOTNULL,
	content XL,
	default_template I1 default 0,
	create_date T,
	modified_date T
");
CmsInstallOperations::create_index($db, 'module_templates', 'module_and_template', 'module_name,template_name');

CmsInstallOperations::create_table($db, 'multilanguage', "
	id I KEY AUTO,
	module_name C(100),
	content_type C(25),
	object_id I,
	property_name C(100),
	language C(5),
	content XL,
	create_date T,
	modified_date T
");

CmsInstallOperations::create_table($db, 'permissions', "
	id I KEY AUTO,
	permission_name C(255),
	permission_text C(255),
	create_date T,
	modified_date T
");

CmsInstallOperations::create_table($db, 'permission_defns', "
	id I KEY AUTO,
	module C(100),
	extra_attr C(50),
	name C(50),
	hierarchical I1 DEFAULT 0,
	link_table C(50)
");

CmsInstallOperations::create_table($db, 'serialized_versions', "
	id I KEY AUTO,
	version I,
	object_id I,
	data B,
	type C(255),
	create_date T,
	modified_date T
");

CmsInstallOperations::create_table($db, 'siteprefs', "
	sitepref_name C(255) KEY,
	sitepref_value text,
	create_date T,
	modified_date T
");

CmsInstallOperations::create_table($db, 'stylesheets', "
	id I KEY AUTO,
	name C(255),
	value XL,
	active I1 default 1,
	media_type C(255),
	create_date T,
	modified_date T
");
CmsInstallOperations::create_index($db, 'stylesheets', 'name', 'name');

CmsInstallOperations::create_table($db, 'stylesheet_template_assoc', "
	stylesheet_id I,
	template_id I,
	order_num I,
	create_date T,
	modified_date T
");
CmsInstallOperations::create_index($db, 'stylesheet_template_assoc', 'stylesheet_id', 'stylesheet_id');
CmsInstallOperations::create_index($db, 'stylesheet_template_assoc', 'template_id', 'template_id');
CmsInstallOperations::create_index($db, 'stylesheet_template_assoc', 'stylesheet_id_template_id', 'stylesheet_id,template_id');

CmsInstallOperations::create_table($db, 'templates', "
	id I KEY AUTO,
	template_name C(255),
	template_content XL,
	encoding C(25),
	active I1 default 1,
	default_template I1,
	create_date T,
	modified_date T
");
CmsInstallOperations::create_index($db, 'templates', 'template_name', 'template_name');

CmsInstallOperations::create_table($db, 'user_groups', "
	group_id I,
	user_id I,
	create_date T,
	modified_date T
");

CmsInstallOperations::create_table($db, 'userprefs', "
	user_id I,
	preference C(50),
	value X,
	type C(25)
");
CmsInstallOperations::create_index($db, 'userprefs', 'user_id', 'user_id');

CmsInstallOperations::create_table($db, 'users', "
	id I KEY AUTO,
	username C(25),
	password C(40),
	admin_access I1,
	first_name C(50),
	last_name C(50),
	email C(255),
	openid C(255),
	checksum C(255),
	active I1,
	create_date T,
	modified_date T
");

CmsInstallOperations::create_table($db, 'userplugins', "
	id I KEY AUTO,
	userplugin_name C(255),
	code X,
	create_date T,
	modified_date T
");

CmsInstallOperations::create_table($db, 'version', "
	version I
");

# vim:ts=4 sw=4 noet
?>
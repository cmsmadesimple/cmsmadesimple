DROP TABLE IF EXISTS {$tableprefix}sections;
DROP TABLE IF EXISTS {$tableprefix}templates;
DROP TABLE IF EXISTS {$tableprefix}pages;
DROP TABLE IF EXISTS {$tableprefix}groups;
DROP TABLE IF EXISTS {$tableprefix}user_groups;
DROP TABLE IF EXISTS {$tableprefix}users;
DROP TABLE IF EXISTS {$tableprefix}permissions;
DROP TABLE IF EXISTS {$tableprefix}group_perms;
DROP TABLE IF EXISTS {$tableprefix}version;

CREATE TABLE {$tableprefix}version (
	version int
);

CREATE TABLE {$tableprefix}permissions (
	permission_id int PRIMARY KEY AUTO_INCREMENT,
	permission_name varchar(25),
	permission_text varchar(255),
	create_date datetime,
	modified_date datetime
);

CREATE TABLE {$tableprefix}group_perms (
	group_perm_id int PRIMARY KEY AUTO_INCREMENT,
	group_id int,
	permission_id int,
	create_date datetime,
	modified_date datetime
);

CREATE TABLE {$tableprefix}templates (
	template_id int PRIMARY KEY AUTO_INCREMENT,
	template_name varchar(25),
	template_content text,
	active bool,
	create_date datetime,
	modified_date datetime
);

CREATE TABLE {$tableprefix}sections (
	section_id int PRIMARY KEY AUTO_INCREMENT,
	section_name varchar(25),
	active bool,
	create_date datetime,
	modified_date datetime
);

CREATE TABLE {$tableprefix}pages (
	page_id int PRIMARY KEY AUTO_INCREMENT,
	page_title varchar(255),
	page_url varchar(255),
	page_content text,
	menu_text varchar(25),
	default_page bool,
	show_in_menu bool,
	owner int,
	active bool,
	section_id int,
	template_id int,
	create_date datetime,
	modified_date datetime
);

CREATE TABLE {$tableprefix}groups (
	group_id int PRIMARY KEY AUTO_INCREMENT,
	group_name varchar(25),
	active bool,
	create_date datetime,
	modified_date datetime
);

CREATE TABLE {$tableprefix}user_groups (
	group_id int,
	user_id int,
	create_date datetime,
	modified_date datetime
);

CREATE TABLE {$tableprefix}users (
	user_id int PRIMARY KEY AUTO_INCREMENT,
	username varchar(25),
	password varchar(25),
	active bool,
	create_date datetime,
	modified_date datetime
);

INSERT INTO {$tableprefix}version VALUES ({$schemaversion});

INSERT INTO {$tableprefix}templates(template_name, template_content, active, create_date, modified_date)
	VALUES('Default', '{literal}<p>Header</p> {bulletmenu} {$content}<p>Footer</p>{/literal}', 1, now(), now());

INSERT INTO {$tableprefix}sections(section_name, active, create_date, modified_date)
	VALUES ('About', 1, now(), now());

INSERT INTO {$tableprefix}pages (page_url, page_title, page_content, menu_text, show_in_menu, owner, active, section_id, template_id, default_page, create_date, modified_date)
	VALUES ('/', 'Home Page', 'Testing content', 'Home Page', 1, 1, 1, 1, 1, 1, now(), now());

INSERT INTO {$tableprefix}groups (group_id, group_name, active, create_date, modified_date)
	VALUES (1, 'Admin', 1, now(), now());

INSERT INTO {$tableprefix}user_groups (group_id, user_id, create_date, modified_date)
	VALUES (1, 1, now(), now());

INSERT INTO {$tableprefix}users (user_id, username, password, active, create_date, modified_date)
	VALUES (1, 'admin', 'admin', 1, now(), now());

INSERT INTO {$tableprefix}permissions VALUES (1,'Add Content','Add New Content',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (2,'Add Group','Add Group',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (3,'Add Template','Add Template',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (4,'Add User','Add User',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (5,'Modify Any Content','Modify Any Content',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (6,'Modify Group','Modify Group',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (7,'Modify Permissions','Modify Permissions for Group',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (8,'Modify Template','Modify Template',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (9,'Modify User','Modify User',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (10,'Remove Content','Remove Content',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (11,'Remove Group','Remove Group',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (12,'Remove Template','Remove Template',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (13,'Remove User','Remove User',now(),now());

INSERT INTO {$tableprefix}group_perms VALUES (1,1,1,now(),now());
INSERT INTO {$tableprefix}group_perms VALUES (2,1,2,now(),now());
INSERT INTO {$tableprefix}group_perms VALUES (3,1,3,now(),now());
INSERT INTO {$tableprefix}group_perms VALUES (4,1,4,now(),now());
INSERT INTO {$tableprefix}group_perms VALUES (5,1,5,now(),now());
INSERT INTO {$tableprefix}group_perms VALUES (6,1,6,now(),now());
INSERT INTO {$tableprefix}group_perms VALUES (7,1,7,now(),now());
INSERT INTO {$tableprefix}group_perms VALUES (8,1,8,now(),now());
INSERT INTO {$tableprefix}group_perms VALUES (9,1,9,now(),now());
INSERT INTO {$tableprefix}group_perms VALUES (10,1,10,now(),now());
INSERT INTO {$tableprefix}group_perms VALUES (11,1,11,now(),now());
INSERT INTO {$tableprefix}group_perms VALUES (12,1,12,now(),now());
INSERT INTO {$tableprefix}group_perms VALUES (13,1,13,now(),now());

DROP TABLE IF EXISTS sections;
DROP TABLE IF EXISTS templates;
DROP TABLE IF EXISTS pages;
DROP TABLE IF EXISTS groups;
DROP TABLE IF EXISTS user_groups;
DROP TABLE IF EXISTS users;

CREATE TABLE templates (
	template_id int PRIMARY KEY AUTO_INCREMENT,
	template_name varchar(25),
	template_content text,
	active bool,
	create_date datetime,
	modified_date datetime
);

CREATE TABLE sections (
	section_id int PRIMARY KEY AUTO_INCREMENT,
	section_name varchar(25),
	active bool,
	create_date datetime,
	modified_date datetime
);

CREATE TABLE pages (
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

CREATE TABLE groups (
	group_id int PRIMARY KEY AUTO_INCREMENT,
	group_name varchar(25),
	active bool,
	create_date datetime,
	modified_date datetime
);

CREATE TABLE user_groups (
	group_id int,
	user_id int,
	create_date datetime,
	modified_date datetime
);

CREATE TABLE users (
	user_id int PRIMARY KEY AUTO_INCREMENT,
	username varchar(25),
	password varchar(25),
	active bool,
	create_date datetime,
	modified_date datetime
);

INSERT INTO templates(template_name, template_content, active, create_date)
	VALUES('Default', '<p>Header</p> {bulletmenu} {$content}<p>Footer</p>', 1, now());

INSERT INTO sections(section_name, active, create_date)
	VALUES ('About', 1, now());

INSERT INTO pages (page_url, page_title, page_content, menu_text, show_in_menu, owner, active, section_id, template_id, default_page, create_date)
	VALUES ('/', 'Home Page', 'Testing content', 'Home Page', 1, 1, 1, 1, 1, 1, now());

INSERT INTO groups (group_name, create_date)
	VALUES ('Admin', now());

INSERT INTO user_groups (group_id, user_id, create_date)
	VALUES (1, 1, now());

INSERT INTO users (username, password, create_date)
	VALUES ('admin', 'admin', now());

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
	stylesheet text,
	active bool,
	create_date datetime,
	modified_date datetime
);

CREATE TABLE {$tableprefix}sections (
	section_id int PRIMARY KEY AUTO_INCREMENT,
	section_name varchar(25),
	item_order int,
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
	page_type varchar(25),
	owner int,
	item_order int,
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

INSERT INTO {$tableprefix}{literal}templates (template_id, template_name, template_content, stylesheet, active, create_date, modified_date) VALUES (1,'Default','<html>\r\n<head>\r\n<title>CMS Made Simple Default Site</title>\r\n{stylesheet}\r\n</head>\r\n\r\n<body>\r\n\r\n<div class=\"leftsidebar\">\r\n<img src=\"images/cmslogo.png\"><p><br>\r\n{bulletmenu}\r\n<p class=\"sectionname\">Admin</p>\r\n<ul>\r\n<li><a href=\"admin\">Login</a>\r\n</ul>\r\n</div>\r\n\r\n<div class=\"thebody\">{content}</div>\r\n{footer}\r\n\r\n</body>\r\n</html>','body { color: #000000;\r\n       background-color: #EDF2F5;\r\n       font-size: 10pt;\r\n       font-family: Tahoma;\r\n       margin: 10; }\r\n\r\ntd.nav     { color: #000000;\r\n             background-color: #e9e9e9;\r\n             font-size: 10pt;\r\n             text-align: center;\r\n             font-family: Tahoma }\r\ntd.header  { color: #000000;\r\n             font-size: 10pt;\r\n             font-family: Tahoma }\r\ntd.label   { color: #000000;\r\n             background-color: #ccccff;\r\n             font-size: 10pt;\r\n             font-family: Tahoma }\r\ntd.body    { color: #000000;\r\n             background-color: #ffffff;\r\n             font-size: 10pt;\r\n             font-family: Tahoma }\r\ntd.bodyalt { color: #000000;\r\n             background-color: #e9e9e9;\r\n             font-size: 10pt;\r\n             font-family: Tahoma }\r\ntd.footer  { color: #000000;\r\n             font-size: 10pt;\r\n             font-family: Tahoma }\r\n\r\na:link     { color: #002060;\r\n             text-decoration: none;\r\n             font-size: 10pt;\r\n             font-family: Tahoma }\r\na:visited  { color: #333333;\r\n             text-decoration: none;\r\n             font-size: 10pt;\r\n             font-family: Tahoma }\r\na:active   { color: #606060;\r\n             text-decoration: none;\r\n             font-size: 10pt;\r\n             font-family: Tahoma }\r\na:hover    { color: #700000;\r\n             text-decoration: none;\r\n             font-size: 10pt;\r\n             font-family: Tahoma }\r\n\r\n\r\n.good     { color: #0033FF;\r\n             font-weight: bold }\r\n.bad      { color: #CC3300;\r\n             font-weight: bold }\r\n\r\ndiv.heading { background-color: #CCCCFF; padding: 9px; text-align: left; font-size: 16; font-weight: bold;  text-transform: uppercase; }\r\ndiv.leftsidebar { float: left; width: 150px; background-color: #ACBFCD;\r\n    font-size: 12px; padding: 7px; border-right: 1px solid #000033;\r\n    border-bottom: 1px solid #000033; color: #444444; border-top: 1px solid #000033; border-left: 1px solid #000033; }\r\ndiv.thebody { padding: 5px; margin-left: 170px; font-size: 10pt; font-family: Tahoma; }\r\ndiv.bottom { background-color: #ACBFCD; border-bottom: 2px solid #000033;\r\n    border-top: 2px solid #000033; border-left: 2px solid #000033;\r\n    border-right: 2px solid #000033; padding: 3px; text-align: center;\r\n    font-size: 11; }\r\np.title { font-size: 14pt; font-weight: bold; margin: 0; }\r\np.smalltitle { font-size: 12pt; font-weight: bold; margin: 0; padding: 4px; background-color: #CCCCCC; text-transform: uppercase; }\r\np.sectionname { font-weight: bold; margin: 0; }\r\n',1, now(), now());{/literal}

INSERT INTO {$tableprefix}{literal}sections (section_id, section_name, item_order, active, create_date, modified_date) VALUES (1,'About',NULL,1,now(),now());{/literal}

INSERT INTO {$tableprefix}{literal}pages (page_id, page_title, page_url, page_content, menu_text, default_page, show_in_menu, page_type, owner, item_order, active, section_id, template_id, create_date, modified_date) VALUES (1,'Home Page','/','<div class=\"heading\">Thank you for installing CMS: CMS Made Simple</div>\r\n<br>\r\nHopefully this default install will give you some helpful tips on maintaining and customizing your CMS website.\r\n\r\n<p>\r\n<p class=\"smalltitle\">Objective</p>\r\n<p>To create a simple and effective way of managing content of primarily static sites.  Before you can ask why you need a content management system for a site that doesn\'t change its content, let me tell you.\r\n<p>\r\nThere are tons of content management systems out there but they are basically all the same.  Other CMS systems are great if you have a lot of news or articles on your site, but what if your site content doesn\'t change a lot, or only small parts of it change?  Doesn\'t make a lot of sense to create a new entry in your blog style site just for a simple update, and what\'s the point of having time stamped blog entries if you are constantly updating them?  Voila, in comes CMS Made Simple.\r\n<p>\r\nCMS lets you update your pages and keep the content on a static page that will not become stale regardless of how much other content gets placed on your site, unlike a blog style site where entries get pushed off the page and your users have to check the archives or know an obscure link to get to the original story.\r\n\r\n<p>\r\n<p class=\"smalltitle\">Features</p>\r\n<ol>\r\n<li>Simple to install\r\n<li>Easily update your content, templates, and CSS files in your web browser\r\n<li>Template driven design allows heavy use of CSS to decorate pages\r\n<li>All group and user management done through a web browser\r\n<li>Friendly support always found on irc.freenode.net in #cms\r\n</ol>\r\n<p>\r\n<p class=\"smalltitle\">Roadmap</p>\r\n<p>\r\nHere are some of the features we are planning to implement post version 0.1:\r\n<ol>\r\n<li>Reusable content blocks\r\n<li>Customizable ordering of content driven menu items\r\n<li>Previewing content changes\r\n</ol>','Home Page',1,1,NULL,1,NULL,1,1,1,now(), now());{/literal}
INSERT INTO {$tableprefix}{literal}pages (page_id, page_title, page_url, page_content, menu_text, default_page, show_in_menu, page_type, owner, item_order, active, section_id, template_id, create_date, modified_date) VALUES (2,'Frequently Asked Questions','/faq','<div class=\"heading\">Frequently Asked Questions</div>\r\n<p>\r\n<ol>\r\n<li><p class=\"sectionname\">This is a content management system, where do I manage my content?</p>\r\nYou have to login as the administrator (default username/pass: admin/admin) on your site at http://yourwebsite.com/cms/admin.<p>\r\nIf you are still viewing this FAQ on your own default install you can probably just click the Login link on the menu.<p>\r\n<li><p class=\"sectionname\">Where do I edit content/menus/templates?</p>\r\nAll these things are handled from the admin section.<p>\r\n<li><p class=\"sectionname\">Where can I get help/support</p>\r\nThe best place to find help is on IRC in #cms on irc.freenode.net<p>\r\n<li><p class=\"sectionname\">How can I help?</p>\r\nWe\'re always looking for design help with logos or CSS work.  Contact us in #cms on irc.freenode.net to get involved.<p>\r\n<li><p class=\"sectionname\">Who wrote CMS?</p>\r\n<ul>\r\n<li>Wishy\r\n<li>Mudfly\r\n<li>Heavy\r\n</ul>\r\n</ol>','FAQ',NULL,1,NULL,1,NULL,1,1,1,now(), now());{/literal}
INSERT INTO {$tableprefix}{literal}pages (page_id, page_title, page_url, page_content, menu_text, default_page, show_in_menu, page_type, owner, item_order, active, section_id, template_id, create_date, modified_date) VALUES (3,'Contact Us','/contact','<div class=\"heading\">Contact Us</div>\r\n<p>\r\nThe best place to find us is in #cms on irc.freenode.net.\r\n<p>\r\nLook for wishy, mudfly, or heavy to answer any questions.','Contact Us',NULL,1,NULL,1,NULL,1,1,1, now(), now());{/literal}
INSERT INTO {$tableprefix}{literal}pages (page_id, page_title, page_url, page_content, menu_text, default_page, show_in_menu, page_type, owner, item_order, active, section_id, template_id, create_date, modified_date) VALUES (4,'SVN (cvs)','/svn','<div class=\"heading\">SVN</div>\r\n<p>\r\nIf you want to be brave feel free to check out the latest version of CMS from the subversion repository.  You can do this from a shell with the following command:<p>\r\nsvn co http://svn.wishy.org/cms/cms/trunk\r\n<p>\r\nThis is a fairly dynamic piece of software that is updated frequently so if you using SVN please be aware that things can and will change and you might even lose some information.  That\'s just the way it works.\r\n<p>\r\nYou can also browse the source in SVN from your browser <a href=\"http://svn.wishy.org/cms/cms/trunk\">here</a>','SVN (cvs)',NULL,1,NULL,1,NULL,1,1,1, now(), now());{/literal}

INSERT INTO {$tableprefix}groups (group_id, group_name, active, create_date, modified_date)
	VALUES (1, 'Admin', 1, now(), now());

INSERT INTO {$tableprefix}user_groups (group_id, user_id, create_date, modified_date)
	VALUES (1, 1, now(), now());

INSERT INTO {$tableprefix}users (user_id, username, password, active, create_date, modified_date)
	VALUES (1, 'admin', 'admin', 1, now(), now());

INSERT INTO {$tableprefix}permissions VALUES (1,'Add Content','Add New Content',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (2,'Add Group','Add Group',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (3,'Add Section','Add Section',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (4,'Add Template','Add Template',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (5,'Add User','Add User',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (6,'Modify Any Content','Modify Any Content',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (7,'Modify Group','Modify Group',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (8,'Modify Group Assignments','Modify Group Assignments',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (9,'Modify Permissions','Modify Permissions for Group',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (10,'Modify Section','Modify Section',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (11,'Modify Template','Modify Template',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (12,'Modify User','Modify User',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (13,'Remove Content','Remove Content',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (14,'Remove Group','Remove Group',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (15,'Remove Section','Remove Section',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (16,'Remove Template','Remove Template',now(),now());
INSERT INTO {$tableprefix}permissions VALUES (17,'Remove User','Remove User',now(),now());

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
INSERT INTO {$tableprefix}group_perms VALUES (14,1,14,now(),now());
INSERT INTO {$tableprefix}group_perms VALUES (15,1,15,now(),now());
INSERT INTO {$tableprefix}group_perms VALUES (16,1,16,now(),now());
INSERT INTO {$tableprefix}group_perms VALUES (17,1,17,now(),now());

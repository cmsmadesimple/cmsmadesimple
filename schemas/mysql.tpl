DROP TABLE IF EXISTS {$tableprefix}additional_users;
DROP TABLE IF EXISTS {$tableprefix}group_perms;
DROP TABLE IF EXISTS {$tableprefix}groups;
DROP TABLE IF EXISTS {$tableprefix}pages;
DROP TABLE IF EXISTS {$tableprefix}permissions;
DROP TABLE IF EXISTS {$tableprefix}sections;
DROP TABLE IF EXISTS {$tableprefix}templates;
DROP TABLE IF EXISTS {$tableprefix}user_groups;
DROP TABLE IF EXISTS {$tableprefix}users;
DROP TABLE IF EXISTS {$tableprefix}version;

CREATE TABLE {$tableprefix}additional_users (
  additional_users_id int(11) NOT NULL auto_increment,
  user_id int(11) default NULL,
  page_id int(11) default NULL,
  PRIMARY KEY  (additional_users_id)
) TYPE=MyISAM;

CREATE TABLE {$tableprefix}group_perms (
  group_perm_id int(11) NOT NULL auto_increment,
  group_id int(11) default NULL,
  permission_id int(11) default NULL,
  create_date datetime default NULL,
  modified_date datetime default NULL,
  PRIMARY KEY  (group_perm_id)
) TYPE=MyISAM;

CREATE TABLE {$tableprefix}groups (
  group_id int(11) NOT NULL auto_increment,
  group_name varchar(25) default NULL,
  active tinyint(1) default NULL,
  create_date datetime default NULL,
  modified_date datetime default NULL,
  PRIMARY KEY  (group_id)
) TYPE=MyISAM;

CREATE TABLE {$tableprefix}pages (
  page_id int(11) NOT NULL auto_increment,
  page_title varchar(255) default NULL,
  page_url varchar(255) default NULL,
  page_content text,
  menu_text varchar(25) default NULL,
  default_page tinyint(1) default NULL,
  show_in_menu tinyint(1) default NULL,
  page_type varchar(25) default NULL,
  owner int(11) default NULL,
  item_order int(11) default NULL,
  active tinyint(1) default NULL,
  section_id int(11) default NULL,
  template_id int(11) default NULL,
  create_date datetime default NULL,
  modified_date datetime default NULL,
  PRIMARY KEY  (page_id)
) TYPE=MyISAM;

CREATE TABLE {$tableprefix}permissions (
  permission_id int(11) NOT NULL auto_increment,
  permission_name varchar(25) default NULL,
  permission_text varchar(255) default NULL,
  create_date datetime default NULL,
  modified_date datetime default NULL,
  PRIMARY KEY  (permission_id)
) TYPE=MyISAM;

CREATE TABLE {$tableprefix}sections (
  section_id int(11) NOT NULL auto_increment,
  section_name varchar(25) default NULL,
  item_order int(11) default NULL,
  active tinyint(1) default NULL,
  create_date datetime default NULL,
  modified_date datetime default NULL,
  PRIMARY KEY  (section_id)
) TYPE=MyISAM;

CREATE TABLE {$tableprefix}templates (
  template_id int(11) NOT NULL auto_increment,
  template_name varchar(25) default NULL,
  template_content text,
  stylesheet text,
  active tinyint(1) default NULL,
  create_date datetime default NULL,
  modified_date datetime default NULL,
  PRIMARY KEY  (template_id)
) TYPE=MyISAM;

CREATE TABLE {$tableprefix}user_groups (
  group_id int(11) default NULL,
  user_id int(11) default NULL,
  create_date datetime default NULL,
  modified_date datetime default NULL
) TYPE=MyISAM;

CREATE TABLE {$tableprefix}users (
  user_id int(11) NOT NULL auto_increment,
  username varchar(25) default NULL,
  password varchar(25) default NULL,
  active tinyint(1) default NULL,
  create_date datetime default NULL,
  modified_date datetime default NULL,
  PRIMARY KEY  (user_id)
) TYPE=MyISAM;

CREATE TABLE {$tableprefix}version (
  version int(11) default NULL
) TYPE=MyISAM;

INSERT INTO {$tableprefix}group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) VALUES (1,1,1,now(),now());
INSERT INTO {$tableprefix}group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) VALUES (2,1,2,now(),now());
INSERT INTO {$tableprefix}group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) VALUES (3,1,3,now(),now());
INSERT INTO {$tableprefix}group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) VALUES (4,1,4,now(),now());
INSERT INTO {$tableprefix}group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) VALUES (5,1,5,now(),now());
INSERT INTO {$tableprefix}group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) VALUES (6,1,6,now(),now());
INSERT INTO {$tableprefix}group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) VALUES (7,1,7,now(),now());
INSERT INTO {$tableprefix}group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) VALUES (8,1,8,now(),now());
INSERT INTO {$tableprefix}group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) VALUES (9,1,9,now(),now());
INSERT INTO {$tableprefix}group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) VALUES (10,1,10,now(),now());
INSERT INTO {$tableprefix}group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) VALUES (11,1,11,now(),now());
INSERT INTO {$tableprefix}group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) VALUES (12,1,12,now(),now());
INSERT INTO {$tableprefix}group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) VALUES (13,1,13,now(),now());
INSERT INTO {$tableprefix}group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) VALUES (14,1,14,now(),now());
INSERT INTO {$tableprefix}group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) VALUES (15,1,15,now(),now());
INSERT INTO {$tableprefix}group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) VALUES (16,1,16,now(),now());
INSERT INTO {$tableprefix}group_perms (group_perm_id, group_id, permission_id, create_date, modified_date) VALUES (17,1,17,now(),now());

INSERT INTO {$tableprefix}groups (group_id, group_name, active, create_date, modified_date) VALUES (1,'Admin',1,now(),now());

INSERT INTO {$tableprefix}{literal}pages (page_id, page_title, page_url, page_content, menu_text, default_page, show_in_menu, page_type, owner, item_order, active, section_id, template_id, create_date, modified_date) VALUES (1,'Home Page','homepage','<div class=\"heading\">Thank you for installing CMS: CMS Made Simple</div>\r\n<br />\r\nHopefully this default install will give you some helpful tips on maintaining and customizing your CMS website.\r\n\r\n<p />\r\n<p class=\"smalltitle\">Objective</p>\r\n<p>To create a simple and effective way of managing content of primarily static sites.  Before you can ask why you need a content management system for a site that doesn\'t change its content, let me tell you.</p>\r\n<p>\r\nThere are tons of content management systems out there but they are basically all the same.  Other CMS systems are great if you have a lot of news or articles on your site, but what if your site content doesn\'t change a lot, or only small parts of it change?  Doesn\'t make a lot of sense to create a new entry in your blog style site just for a simple update, and what\'s the point of having time stamped blog entries if you are constantly updating them?  Voila, in comes CMS Made Simple.</p>\r\n<p>\r\nCMS lets you update your pages and keep the content on a static page that will not become stale regardless of how much other content gets placed on your site, unlike a blog style site where entries get pushed off the page and your users have to check the archives or know an obscure link to get to the original story.</p>\r\n\r\n<p />\r\n<p class=\"smalltitle\">Features</p>\r\n<ol>\r\n<li>Simple to install</li>\r\n<li>Easily update your content, templates, and CSS files in your web browser</li>\r\n<li>Template driven design allows heavy use of CSS to decorate pages</li>\r\n<li>All group and user management done through a web browser</li>\r\n<li>Friendly support always found on irc.freenode.net in #cms</li>\r\n</ol>\r\n<p />\r\n<p class=\"smalltitle\">Roadmap</p>\r\n<p />\r\nHere are some of the features we are planning to implement post version 0.1:\r\n<ol>\r\n<li>Reusable content blocks</li>\r\n<li>Customizable ordering of content driven menu items</li>\r\n<li>Previewing content changes</li>\r\n<li>More plugins for your enjoyment</li>\r\n</ol>\r\n<p />\r\n<p class=\"smalltitle\">License</p>\r\n<p>\r\nCMS is released under the <a href=\"http://www.gnu.org/licenses/licenses.html#GPL\">GPL</a> license</p>','Home Page',1,1,NULL,1,1,1,1,1,now(),now()){/literal};
INSERT INTO {$tableprefix}{literal}pages (page_id, page_title, page_url, page_content, menu_text, default_page, show_in_menu, page_type, owner, item_order, active, section_id, template_id, create_date, modified_date) VALUES (2,'Frequently Asked Questions','faq','<div class=\"heading\">Frequently Asked Questions</div>\r\n\r\n<div>\r\n<ol>\r\n<li><p class=\"sectionname\">This is a content management system, where do I manage my content?</p>\r\n<p>You have to login as the administrator (default username/pass: admin/admin) on your site at http://yourwebsite.com/cms/admin.</p>\r\n<p>If you are still viewing this FAQ on your own default install you can probably just click the Login link on the menu.</p></li>\r\n<li><p class=\"sectionname\">Where do I edit content/menus/templates?</p>\r\n<p>All these things are handled from the admin section.</p></li>\r\n<li><p class=\"sectionname\">Ok, I\'m in the admin section.  What should I do first?</p>\r\n<p>Change the admin password!  Anyone can come along and get admin control over your system just by knowing the default password!  Stop reading this and change it this instant!</p></li>\r\n<li><p class=\"sectionname\">Where can I get help/support</p>\r\n<p>The best place to find help is on IRC in #cms on irc.freenode.net</p></li>\r\n<li><p class=\"sectionname\">How can I help?</p>\r\n<p>We love designers!  We\'re always looking for design help with logos, CSS work or templates to include in the default install.  Contact us in #cms on irc.freenode.net to get involved.</p></li>\r\n<li><p class=\"sectionname\">Who wrote CMS?</p>\r\n<ul>\r\n<li>Wishy</li>\r\n<li>Mudfly</li>\r\n<li>Heavy</li>\r\n</ul>\r\n</li>\r\n</ol>\r\n</div>','FAQ',NULL,1,NULL,1,2,1,1,1,now(),now()){/literal};
INSERT INTO {$tableprefix}{literal}pages (page_id, page_title, page_url, page_content, menu_text, default_page, show_in_menu, page_type, owner, item_order, active, section_id, template_id, create_date, modified_date) VALUES (3,'Contact Us','contact','<div class=\"heading\">Contact Us</div>\r\n<p>\r\nThe best place to find us is in #cms on irc.freenode.net.</p>\r\n<p>\r\nLook for wishy, mudfly, or heavy to answer any questions.</p>','Contact Us',NULL,1,NULL,1,4,1,1,1,now(),now()){/literal};
INSERT INTO {$tableprefix}{literal}pages (page_id, page_title, page_url, page_content, menu_text, default_page, show_in_menu, page_type, owner, item_order, active, section_id, template_id, create_date, modified_date) VALUES (4,'SVN (cvs)','svn','<div class=\"heading\">SVN</div>\r\n<p>\r\nIf you want to be brave feel free to check out the latest version of CMS from the subversion repository.  You can do this from a shell with the following command:</p>\r\n<p>\r\nsvn co http://svn.wishy.org/cms/cms/trunk</p>\r\n<p>\r\nThis is a fairly dynamic piece of software that is updated frequently so if you using SVN please be aware that things can and will change and you might even lose some information.  That\'s just the way it works.</p>\r\n<p>\r\nYou can also browse the source in SVN from your browser <a href=\"http://svn.wishy.org/cms/cms/trunk\">here</a></p>','SVN (cvs)',NULL,1,NULL,1,3,1,1,1,now(),now()){/literal};

INSERT INTO {$tableprefix}permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES (1,'Add Content','Add New Content',now(),now());
INSERT INTO {$tableprefix}permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES (2,'Add Group','Add Group',now(),now());
INSERT INTO {$tableprefix}permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES (3,'Add Section','Add Section',now(),now());
INSERT INTO {$tableprefix}permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES (4,'Add Template','Add Template',now(),now());
INSERT INTO {$tableprefix}permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES (5,'Add User','Add User',now(),now());
INSERT INTO {$tableprefix}permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES (6,'Modify Any Content','Modify Any Content',now(),now());
INSERT INTO {$tableprefix}permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES (7,'Modify Group','Modify Group',now(),now());
INSERT INTO {$tableprefix}permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES (8,'Modify Group Assignments','Modify Group Assignments',now(),now());
INSERT INTO {$tableprefix}permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES (9,'Modify Permissions','Modify Permissions for Group',now(),now());
INSERT INTO {$tableprefix}permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES (10,'Modify Section','Modify Section',now(),now());
INSERT INTO {$tableprefix}permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES (11,'Modify Template','Modify Template',now(),now());
INSERT INTO {$tableprefix}permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES (12,'Modify User','Modify User',now(),now());
INSERT INTO {$tableprefix}permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES (13,'Remove Content','Remove Content',now(),now());
INSERT INTO {$tableprefix}permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES (14,'Remove Group','Remove Group',now(),now());
INSERT INTO {$tableprefix}permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES (15,'Remove Section','Remove Section',now(),now());
INSERT INTO {$tableprefix}permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES (16,'Remove Template','Remove Template',now(),now());
INSERT INTO {$tableprefix}permissions (permission_id, permission_name, permission_text, create_date, modified_date) VALUES (17,'Remove User','Remove User',now(),now());

INSERT INTO {$tableprefix}sections (section_id, section_name, item_order, active, create_date, modified_date) VALUES (1,'About',NULL,1,now(),now());


INSERT INTO {$tableprefix}{literal}templates (template_id, template_name, template_content, stylesheet, active, create_date, modified_date) VALUES (1,'Default','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\r\n<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" >\r\n<head>\r\n<title>CMS Made Simple Default Site</title>\r\n{stylesheet}\r\n</head>\r\n\r\n<body>\r\n\r\n<div class=\"leftsidebar\">\r\n\r\n<div class=\"icons\">\r\n<p><img src=\"images/cmslogo.png\" alt=\"CMS Logo\" /></p>\r\n</div>\r\n\r\n{bulletmenu}\r\n\r\n<p class=\"sectionname\">Admin</p>\r\n<ul>\r\n<li><a href=\"admin\">Login</a></li>\r\n</ul>\r\n</div>\r\n\r\n<div class=\"thebody\">\r\n{content}\r\n</div>\r\n\r\n<!-- Before Footer -->\r\n\r\n<div class=\"thebody\">{footer}</div>\r\n\r\n</body>\r\n</html>','body { color: #000000;\r\n       background-color: #fff;\r\n       font-size: 10pt;\r\n       font-family: Tahoma;\r\n       margin: 10; }\r\n\r\ntd.nav     { color: #000000;\r\n             background-color: #fff;\r\n             font-size: 10pt;\r\n             text-align: center;\r\n             font-family: Tahoma }\r\ntd.header  { color: #000000;\r\n             font-size: 10pt;\r\n             font-family: Tahoma }\r\ntd.label   { color: #000000;\r\n             background-color: #ccccff;\r\n             font-size: 10pt;\r\n             font-family: Tahoma }\r\ntd.body    { color: #000000;\r\n\r\n             background-color: #ffffff;\r\n             font-size: 10pt;\r\n             font-family: Tahoma }\r\n\r\ntd.bodyalt { color: #000000;\r\n             background-color: #e9e9e9;\r\n             font-size: 10pt;\r\n             font-family: Tahoma }\r\ntd.footer  { color: #000000;\r\n             font-size: 10pt;\r\n             font-family: Tahoma }\r\n\r\na:link     { color: #002060;\r\n             text-decoration: none;\r\n             font-size: 10pt;\r\n             font-family: Tahoma }\r\na:visited  { color: #333333;\r\n             text-decoration: none;\r\n             font-size: 10pt;\r\n             font-family: Tahoma }\r\na:active   { color: #606060;\r\n             text-decoration: none;\r\n             font-size: 10pt;\r\n             font-family: Tahoma }\r\na:hover    { color: #700000;\r\n             text-decoration: none;\r\n             font-size: 10pt;\r\n             font-family: Tahoma }\r\n\r\n\r\n\r\n\r\n.good     { color: #0033FF;\r\n             font-weight: bold }\r\n.bad      { color: #CC3300;\r\n             font-weight: bold }\r\n\r\ndiv.heading {\r\n    background-color: #64879d; \r\n    color: #EDF2F5;\r\n    padding: 9px; \r\n    text-align: left; \r\n    font-size: 16px; \r\n    font-weight: bold;  \r\n    text-transform: uppercase; \r\n}\r\ndiv.leftsidebar { \r\n    float: left; \r\n    width: 120px; \r\n    background-color: #EDF2F5;\r\n    font-size: 12px; \r\n    padding: 7px; \r\n    border: 1px solid #64879d;\r\n    color: #444444; \r\n}\r\ndiv.thebody { \r\n    padding: 5px; \r\n    margin-left: 150px; \r\n    font-size: 10pt; \r\n    font-family: Tahoma; \r\n}\r\ndiv.bottom {\r\n    background-color: #EDF2F5; \r\n    border: 1px solid #64879d;\r\n    padding: 3px; \r\n    text-align: center;\r\n    font-size: 11; \r\n}\r\np.title { \r\n    font-size: 14pt; \r\n    font-weight: bold; \r\n    margin: 0; \r\n}\r\np.smalltitle { \r\n    font-size: 12pt; \r\n    font-weight: bold; \r\n    margin: 0; \r\n    padding: 4px; \r\n    background-color: #CCCCCC; \r\n    text-transform: uppercase; \r\n}\r\np.sectionname { \r\n    font-weight: bold; \r\n    margin: 0;\r\n}\r\n.leftsidebar p.sectionname {\r\n    padding-left: 15px;\r\n}\r\n.icons {\r\n    text-align: center;\r\n}',1,now(),now()){/literal};

INSERT INTO {$tableprefix}user_groups (group_id, user_id, create_date, modified_date) VALUES (1,1,now(),now());

INSERT INTO {$tableprefix}users (user_id, username, password, active, create_date, modified_date) VALUES (1,'admin','admin',1,now(),now());

INSERT INTO {$tableprefix}version (version) VALUES (2);


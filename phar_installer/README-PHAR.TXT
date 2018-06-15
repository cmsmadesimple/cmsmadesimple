--------------------------------------------------
CMS Made Simple PHAR Based Installation Assistant
--------------------------------------------------
This document describes using the CMS Made Simple PHAR Based installation assistant.

The PHAR based installation assistant is an entire PHP application built to provide the ability to
install, upgrade, or freshen CMS Made Simple from within a single, easy to use php script.

---------------------------
NOTE:
---------------------------
The PHAR based installation assistant is a binary file and must be transferred in binary mode!

---------------------------
Requirements
---------------------------
1.  The installation assistant requires a PHP environment with a minimum version of PHP 5.4.0 with the Phar library enabled.
2.  For new installations of CMS Made Simple you should create a new mysql database and database user.
    - The database user must have ALL PRIVILEGES to all tables within the new database.
    - Your database user account must have a password.
3.  CMS Made Simple itself requires at least PHP 5.6 with numerous libraries enabled (the install assistant will check for these) including json, tokenizer, xml, and gd.
4.  Your PHP environment must include the PHAR extension

---------------------------
Instructions
---------------------------
1.  Upload the cmsms-<version>-install.zip file to your web directory.
2.  Extract the files from the .zip file
    This will create a cmsms-<version>-install.php file, and another copy of this README file.
3.  Using your browser, navigate to <your web directory>/cmsms-<version>-install.php
    For Example: http://www.mywebsite.site/cmsms-2.0-install.php

You should now be presented with a welcome screen.  If, instead, you see a white screen it probably means that your
server is running old, or incompatible software and that you may be forced to use the traditional installer.  It is a good
hint that if the Phar installer will not run you may have further problems with CMS Made Simple on that server.

The installation assistant can then be used to guide you through the process of installing a new version of CMSMS,
Upgrading an existing installation of CMSMS, or freshening an existing version of CMSMS. There are various options
for each path through the installation assistant and 9 steps

Step 1:
  Step 1 asks you to select a language and optionally allows you to enable "Advanced Mode". Advanced mode enables various
  additional options throughout the assistant and increases the verbosity of the status output.

Step 2:
  The second step does checks for existing software in your installation directory.  If an existing installation of
  CMS Made Simple is detected you will be presented with options to proceed through the "upgrade" or "freshen" paths
  (depending upon the version detected). If no version of CMS Made Simple is installed you will be guided through the
  "installation" process.

Step 3:
  Step 3 performs various tests on your PHP environment to try to ensure that the PHP environment is compatible with
  CMS Made Simple. If important tests fail you will be notified of them and not allowed to continue. However, if
  some non critical tests fail you will be allowed to continue. We recommend that you adjust your PHP environment
  (you may need to contact your host for assistance) until all tests pass.

Step 4:
  This step is used only during the installation or freshen paths. It asks you to provide basic configuration information
  for the CMS Made Simple environment. This includes database information and credentials, and a server time zone.

  As mentioned in the requirements above. CMSMS requires a mysql database to store data, and user credentials to access
  it.  The user account provided must have ALL PRIVILEGES to the database.  You can normally create
  databases, and create user accounts for database from within your web host's control panel.

  Additionally, you are asked to specify the time zone of the server (not your local time zone). You may need to ask your
  host for information about this.

  Upon submit, the installation assistant will validate your database credentials, and check the database to ensure
  you are not accidentally overwriting an existing installation of CMSMS.

Step 5:
  This step is used only during the installation path.  It asks you to provide basic credentials to the first Admin user
  account. This Admin user account allows you to login to the CMSMS Admin console with all privileges and access to all
  of its functionality. From within the CMSMS Admin console you can create user groups, and further user accounts
  for additional site managers or editors.

  Unless you have enabled "Advanced mode" in step 1, you must also provide an email address. The email address will be
  used to send you your login credentials in the event of a lost or forgotten password.  An email will also be sent to you
  with your initial login credentials.

Step 6:
  This step asks you for a human readable name for your website, and allows you to select additional language packs to install.

  Additional language packs allow users who login to the CMSMS Admin console to display the output in their native language.
  Please note: though there are multiple languages available, not all of them are complete...

Step 7:
  This step copies all of the CMSMS core files from within the installation assistant into your CMS Made Simple install. It also
  cleans up those files.

Step 8:
  This step interacts with the database to ensure that all tables and initial content are created properly.

  During the installation process numerous tables and indexes are created in the database, and necessary initial data is installed.
  Additionally (by default) sample data is installed.  The sample data provides useful instructions on how to interact with,
  and build sites with, CMS Made Simple. It is recommended reading.

  During the upgrade process, any necessary database changes are preformed to ensure that the database tables, and their contents,
  are compatible with the new version of CMSMS.

Step 9:
  This step finishes the installation and performs a clean-up.

  During the installation process all core modules are installed, necessary temporary directories created, and the configuration file
  is written.

  During the upgrade process core modules are upgraded if necessary, and a new version of the configuration file is written.
  Any existing configuration file is backed up for safety.  Additionally, the CMSMS cache is cleared.

  Once everything is complete you will be presented with links to either visit your CMSMS website, or to login to its Admin console.


---------------------------
Afterwards
---------------------------
After successful completion of the installation assistant, you should delete the installation assistant .php file.
It is important for security purposes that you delete the installation assistant .php file and this text file from your server after verifying that the operation has succeeded.

---------------------------
Additional Features
---------------------------
The single installation assistant package provides these features:
1.  Installing a new version of CMSMS into an empty directory.
2.  Upgrading an existing version of CMSMS to a new version
3.  Freshening (or Repairing) an existing installation of CMSMS
    This option may be useful for fixing a corrupted installation of CMSMS.  Only available when the current version of CMSMS and the new version are identical, it will replace all of the core files with those distributed by the system and re-generate a new config.php file.
4.  Installing additional language packages
    As part of the "Freshen" functionality, when repairing an installation you have the option to install additional language packs.
5.  Advanced vs. Simple mode

---------------------------
Available Options
---------------------------
The installation assistant supports the following URL based options:
1.  TMPDIR=/absolute/path
    Specify the absolute path to the directory to be used for temporary files.  This directory must be writable by the PHP process.  This option should not be needed on most hosts.
2.  debug=1
    Attempt Turn on error reporting, and to display some meaningful help information.
3.  nobase=1
    Do not output a base href tag in the generated HTML.
4.  dest=/absolute/path
    Allow specifying a custom destination directory (must be an absolute path).
5.  nofiles=1
    Do not overwrite files.  This is useful when needing to setup the database when the files have been manually extracted from the source code repository.
6.  clear=1
    Usable only on the first step, this will ensure that all preset config information is cleared from the session (for development purposes only).


---------------------------
Frequently Asked Questions
---------------------------
Q: What is a PHAR?
A: A Phar is a single, self contained, executable PHP Archive.  It allows us to distribute the CMSMS installation assistant as a single file even though it contains numerous libraries, classes, stylesheets, and scripts. This allows CMSMS users to install, upgrade or freshen their CMSMS systems by  uploading a single file to their web server.

Q: Why is there a .php file inside the archive, and not a .phar?
A: Most web servers are not configured to treat files ending with .phar as executable PHP scripts.  Therefore we have renamed the file as a .php file so that web servers will know to execute the script.  Please note however, that this is a binary file and must be treated as such.

Q:  How do I upload this thing via FTP?
A:  We do not recommend FTP as a file transfer mechanism.  In fact, we highly discourage it.

    If your only mechanism to transfer files is via FTP because your host does not support shell/sftp or does not provide a reasonable web control panel with upload and unzip capabilities, then that could indicate that the host is not running up-to-date software, and you may have further difficulties.  Consider evaluating and moving to a different host.

    If you still insist on using FTP then use a decent FTP client (such as filezilla) and ensure that you transfer ALL files in binary mode.

Q: I get a message saying: "unable to create temporary file for decompression of gzipped phar archive"
A: Some hosts have restricted PHP's write access to the system temporary directory, which the installer needs to expand archives.  However there is a mechanism to use a different temporary directory for the installer.
   You can add a TMPDIR=/absolute/path/to/writable/directory argument to the URL on the first page.  If this directory is writable it will be used for storing temporary files throughout the execution of the installation assistant.

Q: The installation assistant says it cannot write to all files in the directory.  Why?
A: The installation assistant is capable of upgrading from different different versions of CMSMS, and to do that it must be able to create, update, and delete files.  Including the config.php file.  In order to do that the PHP processor it must be able to write to each and every file and directory in the installation directory.

   Additionally, the installation assistant will change the permissions on the config.php file so that by default it is protected.  When performing an installation, or an upgrade you may need to manually change these permissions so that the installation assistant can adjust your config.php file.

Q: I Get a white screen, what can I do?
A: A white screen indicates an error of some sort.  It can be caused by permissions problems, an incompatible host, or something else.  There are a few things to try:
   1.  Try to browse to the README-PHAR.TXT file with your browser.  If that also generates a white screen then it is indicative of permissions or .htaccess limitations.
       Note:  Depending upon host configuration, Some hosts do not allow browseable files to be writable, or even readable by users other than the file owner.  You can try changing the permissions of the extracted files to 600.
   2.  Rename your .htaccess file(s) out of the way.  (Rewrite rules and bad configuration options in the .htaccess file can cause 500 errors).
   3.  Enable and find your php error logs so that you can find an actual error message to aide in further diagnosis.
   4.  Add ?debug=1 to the URL for the installer.  This will attempt to enable a simple debug mode.  This, depending on your host configuration may allow you to see error messages.

Q:  I still get a white screen, and I've enabled debug mode... now what?
A:  This could indicate that either your installer upload is corrupted OR that your host does not allow you to change debug settings in the standard way.   You will probably have to contact your host in order to get at the actual error.

Q:  I Get a redirection loop, what can I do?
A:  Some hosting environments (nginx, and litespeed) particularly can cause redirection loops when executing even the simplest of phar archives.  This is due to one or more bugs in php.  Specifically: https://bugs.php.net/bug.php?id=71465 and https://bugs.php.net/bug.php?id=67587 (though more may exist).  For these environments we recommend that you use the expanded installer.  Be sure you read it's README file in it's entirety.

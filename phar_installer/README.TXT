---------------------------------------
CMS Made Simple Expanded Installation Assistant
---------------------------------------
This document describes using the expanded CMS Made Simple installation assistant.

As opposed to the PHAR based installation assistant, this version is not compressed into a single archive file
and contains numerous files and directories.  It is useful for installing, upgrading and freshening CMSMS installs
on servers which are not necessarily running up-to-date software or those with special configurations that do not support
using the simpler phar installer.


-------------------
WARNINGS:
-------------------
1.  We DO NOT recommend the use of this version of the installation assistant.  Please use the PHAR version of the installer
    whenever possible.   When using this installer, please execute extreme caution.

2.  DO NOT INSTALL THIS PACKAGE IN A POPULATED DIRECTORY.
    At all times you MUST use a clean directory for the expanded version of the installation assistant.  This clean directory
    can be located below an existing CMSMS install,  or parallel to it.


-------------------
Requirements:
-------------------
1.  The installation assistant requires a PHP environment with a minimum version of PHP 5.4.0.
2.  For new installations of CMS Made Simple you should create a new mysql database and database user.
    - The database user must have ALL PRIVILEGES to all tables within the new database.
    - Your database user account must have a password.
3.  CMS Made Simple itself requires at least PHP 5.4.11 with numerous libraries enabled (the install assistant will
    check for these) including json, tokenizer, xml, and gd.
4.  Your PHP environment must include the PHAR extension (yes, even for the expanded installer).  This is for expanding the archive(s) included within the installation assistant.


-------------------
Instructions:
-------------------
1.  Using your web host's file manager (usually accessible within the control panel), create a new directory below
    the location where you want to install CMSMS.  Typically, you could name this directory cmsms_install
    For example:  /home/<myusername>/public_html/cmsms_install

2.  Upload the cmsms-<version>-install.expanded.zip file to this subdirectory.

3.  Extract the files from the .zip archive into this subdirectory.
    This will create numerous files and directories.  So use caution that you are extracting the files into the proper directory.

3.  Using your browser, navigate to the directory created in step 1.
    For Example:   http://www.mywebsite.site/cmsms_install

You should now be presented with a welcome screen.  If instead you see a white screen it probably means that your
server is running outdated software versions (e.g. PHP, plugins such as ion-cube or zend-guard). It is a good hint that
if the installer will not run you may have further problems with CMS Made Simple on that server.

The installation assistant can then be used to guide you through the process of installing a new version of CMSMS,
Upgrading an existing installation of CMSMS, or freshening an existing version of CMSMS.   There are various options
for each path through the installation assistant and 9 steps

Step 1:
  The first step asks you to select an installation directory (the default value is your current directory's parent),
  to select a language for use in the remainder of the installer,  and optionally allows you to enable "Advanced Mode".

  -------
  WARNING Use extreme caution and ensure that the destination directory you select is correct.
  -------

  Advanced mode enables various additional options throughout the assistant and increases the verbosity of the status output.

Step 2:
  The second step does checks for existing software in your installation directory.  If an existing installation of
  CMS Made Simple is detected you will be presented with options to proceed through the "upgrade" or "freshen" paths
  (depending upon the version detected).  If no version of CMS Made Simple is installed you will be guided through the
  "installation" process.

Step 3:
  Step 3 performs various tests on your PHP environment to try to ensure that it is compatible with
  CMS Made Simple.   If important tests fail, you will be notified of them and not allowed to continue.  However, if
  some non-critical tests fail you will be allowed to continue.   We recommend that you adjust your PHP environment
  (you may need to contact your host for assistance) until all tests pass.

Step 4:
  This step is used only during the installation, and freshen paths.  It asks you to provide basic configuration information
  for the CMS Made Simple environment.  This includes database information and credentials, and a server time zone.

  As mentioned in the requirements above,  CMSMS requires a mysql database to store data, and user credentials to access
  that mysql database.  The user account provided must have ALL PRIVILEGES to the database.  You can normally create
  databases, and create user accounts for database from within your web host's control panel.

  Additionally, you are asked to specify the time zone of the server (not your local time zone).  You may need to ask your
  host for information about this.

  Upon submit, the installation assistant will validate your database credentials, and check the database to ensure
  you are not accidentally overwriting an existing installation of CMSMS.

Step 5:
  This step is used only during the installation path.  It asks you to provide basic credentials to the first Admin user
  account.  This Admin user account allows you to login to the CMSMS Admin console with all privileges and access to all
  of its functionality.   From within the CMSMS Admin console you can create user groups, and further user accounts
  for additional site managers or editors.

  Unless you have enabled "Advanced mode" in step 1, you must also provide an email address.  The email address will be
  used to send you your login credentials in the event of a lost or forgotten password.  An email will also be sent to you
  with your initial login credentials.

Step 6:
  This step asks you for a human readable name for your website, and allows you to select additional language packs to install.

  Additional language packs allow users who login to the CMSMS Admin console to display the output in their native language.
  Please note: though there are multiple languages available, not all of them are complete.

Step 7:
  This step copies all of the CMSMS Core files from within the installation assistant into your CMS Made Simple install. it also
  cleans up those files.

Step 8:
  This step interacts with the database to ensure that all tables and initial content are created properly.

  During the installation process numerous tables and indexes are created in the database, and necessary initial data is installed.
  Additionally (by default) sample data is installed.  The sample data provides useful instructions on how to interact with,
  and build sites with, CMS Made Simple. This is recommended reading.

  During the upgrade process, any necessary database changes are preformed to ensure that the database tables, and thier contents,
  are compatible with the new version of CMSMS.

Step 9:
  This step finishes the installation and performs a clean up.

  During the installation process all core modules are installed, necessary temporary directories created, and the configuration file
  is written.

  During the upgrade process core modules are upgraded if necessary, and a new version of the configuration file is written.
  Any existing configuration file is backed up for safety.  Additionally, the CMSMS cache is cleared.

  Once everything is complete you will be presented with links to either visit your CMSMS website, or to login to its Admin console.

-----------
Afterwards
-----------
After successful completion of the installation assistant, you should, using either the command line or your web hosts file manager,
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
The installation assistant supports the following URL based options
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

----
FAQ:
----
Q: The installation assistant says it cannot write to all files in the directory.  Why?
A: The installation assistant is capable of upgrading from different different versions of CMSMS, and to do that it must be able to create, update, and delete files.  Including the config.php file.  In order to do that the PHP processor it must be able to write to each and every file and directory in the installation directory.

   Additionally, the installation assistant will change the permissions on the config.php file so that by default it is protected.  When performing an installation, or an upgrade you may need to manually change these permissions so that the installation assistant can adjust your config.php file.

Q: I get a message saying: "unable to create temporary file for decompression of gzipped phar archive"
A: Some hosts have restricted PHP's write access to the system temporary directory, which the installer needs to expand archives.  However there is a mechanism to use a different temporary directory for the installer.
   You can add a TMPDIR=/absolute/path/to/writable/directory argument to the URL on the first page.  If this directory is writable it will be used for storing temporary files throughout the execution of the installation assistant.

Q: I Get a white screen, what can I do?
A: A white screen indicates an error of some sort.  It can be caused by permissions problems, an incompatible host, or something else.  There are a few things to try:
   1.  Try to browse to the README-PHAR.TXT file with your browser.  If that also generates a white screen then it is indicative of permissions or .htaccess limitations.
       Note:  Depending upon host configuration, Some hosts do not allow browseable files to be writable, or even readable by users other than the file owner.  You can try changing the permissions of the extracted files to 600.
   2.  Rename your .htaccess file(s) out of the way.  (Rewrite rules and bad configuration options in the .htaccess file can cause 500 errors).
   3.  Enable and find your php error logs so that you can find an actual error message to aide in further diagnosis.
   4.  Add ?debug=1 to the URL for the installer.  This will attempt to enable a simple debug mode.  This, depending on your host configuration may allow you to see error messages.

Q:  I still get a white screen, and I've enabled debug mode... now what?
A:  This could indicate that either your installer upload is corrupted OR that your host does not allow you to change debug settings in the standard way.   You will probably have to contact your host in order to get at the actual error.

Q:  How do I upload this thing via FTP?
A:  We do not recommend FTP as a file transfer mechanism.  In fact, we highly discourage it.

    If your only mechanism to transfer files is via FTP because your host does not support shell/sftp or does not provide a reasonable web control panel with upload and unzip capabilities, then that could indicate that the host is not running up-to-date software, and you may have further difficulties.  Consider evaluating and moving to a different host.

    If you still insist on using FTP then use a decent FTP client (such as filezilla) and ensure that you transfer ALL files in binary mode.

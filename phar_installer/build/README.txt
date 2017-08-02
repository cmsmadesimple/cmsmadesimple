------------------------
Creating a CMSMS Release
------------------------


Creating a CMSMS release involves these steps:
  a: Do all of the required changes to the CMSMS branch in question (change the version.php, update the changelog, make sure those files are committed)
  b: Create the <installer root>/app/upgrade/<version> directory and it's appropriate files
     MANIFEST.DAT -- this file is created with the 'create_manifest.php' script (see below)
     (a MANIFEST.DAT.GZ file is acceptable too)
     upgrade.php  -- (optional) the script to do any changes to the database or settings
        note: when this script is executed $db is available, the CMSMS api is created, however smarty is not available.
     readme.txt   -- (optional) readme file for display in the upgrade assistant
     changelog.txt -- (recommended) a text file describing specific changes to that version
     preprocess_files.php -- (optional) executed at the start of step 7 (if files step is enabled) to perform various tasks related to files
       This is useful if files must be moved around within the installation, and the manifest process cannot do it automatically.
       note: when this script is executed $destdir is available, however the CMSMS api is not.
  c: optionally delete directories from <installer root>/app/upgrade that are no longer necessary.
  d: optionally changing <installer root>/app/config.ini to specify the minimum upgrade version.
  d: commit those changes to SVN
  e: build the release packages (see below)
  f: ** Begin distribution process **
     - remember to create an svn tag if distributing

---------------------
Building the manifest
---------------------
  a: execute the create_manifest.php script 
     - this requires the php-cli package
     - requires subversion installed
     - assumes unix/linux
     - this script requires an svn root directory, a from subpath, and a to subpath
       i.e: 
         root directory:  http://svn.cmsmadesimple.org/svn/cmsmadesimple
         from subpath:    branches/1.11.x
         to subpath:      trunk
     - the script exports the two directories, and (accounting for files that need to be excluded) compares the directories to
       find files that have been added/changed/deleted.
  b: copy the generated MANIFEST.DAT.GZ file into the <root>/app/upgrade/<version> directory

-----------------------------
Building the release packages
-----------------------------
1.  Change dir into the build directory
    Note:  You only need the phar_installer directory to do a build... but use caution that it is from the proper branch of cmsms.

2.  Executebuild_release.php
    -- execute build_release.php -h for help
    ** This script is only tested on linux (I'm allergic to windoze)
 
    ** This script requires a bit of configuration
   
    a: requires the php-cli package
       (from ubuntu:  sudo apt-get install php5-cgi)

    b: requires that the php-cli package be allowed (in its configuration) to create phar files
       (from ubuntu:  vi /etc/php5/cli/php.ini; set phar.readonly = Off;)
    
    c: requires subversion to be installed and configured
       (from ubuntu:  sudo apt-get install subversion)

    d: requires zip to be installed and configured
       (from ubuntu:  sudo apt-get install zip)
 
    ** This script executes multiple steps

    a: Exports the CMSMS svn from the path specified in the svn_url at the top of the script
       The path is hard coded to the cmsmadesimple trunk (for now)

    b: It will then clean all of the files that do not belong in the release
       (i.e: remove scripts, tests, svn and git files, backup files etc)

    c: Checksum files are created in the out directory.
 
    d: Resulting files are tarred up into data/data.tar.gz
       The version.php file for the trunk version is also copied here for convenience in knowing what the user will be installing or upgrading to.

    e: A self contained executable .phar file is created and renamed to .php (because most http servers don't accept .phar extensions by default)
   
    f: The .php file is encapsulated into a .zip file (this makes the file easy to share on a server, as the http server won't try to execute it)

    g: The installer, and the data.tar.gz is zipped into a zip file (allows CMSMS to be installed on older systems)

    

-----------------------
Running the .phar file
-----------------------

Most Apache servers are not configured (by default) to execute php for .phar files.  so there are two
solutions:
  1.  Rename the .phar to .php and then browse to it.
      (the build_release script does that, and then encapsulates the .php file into a .zip file)

  2.  Tell Apache to include .phar files in it's executable list. 
      i.e: add this to the .htaccess (may require changing for different server configs)

      <FilesMatch "\.ph(ar|p3?|tml)$">
        SetHandler application/x-httpd-php
      </FilesMatch>

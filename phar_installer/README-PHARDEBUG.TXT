Quick notes for debugging/developing the .phar installer

1:
--
For developing with the .phar installer without building the thing for
each test, specify a
 ?dest=/full/path/to/siterootdirectory
argument in the INITIAL url e.g:
  http://www.mysite.com/phar_installer/index.php?dest=/var/www/cmsms_dir

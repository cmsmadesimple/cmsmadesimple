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
#BUT withOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

/**
 * Class to provide archive extraction and creation
 *
 * @author Robert Campbell
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsArchive extends CmsObject
{
  private $_obj; // archiving/de-archiving object
  private $_excludes;

  const ARCHIVE_TAR  = 'tar';
  const ARCHIVE_GZIP = 'tgz';
  const ARCHIVE_BZIP = 'bz2';
  #const ARCHIVE_ZIP  = 'zip';

  /**
   * Construct a new CmsArchive object
   *
   * @param string Name of the destination (if writing) or source (if reading) archive
   * @param string Type of archive to read/write.  One of ARCHIVE_TAR, ARCHIVE_GZIP or ARCHIVE_BZIP
   **/
  public function __construct($filename,$type = '')
  {
    parent::__construct();
    require_once(cms_join_path(CmsConfig::get('root_path').'/lib/archive/archive.php'));

    if( empty($filename) )
      {
	throw new Exception('invalid parameter');
      }

    if( empty($type) )
      {
	$type = CmsArchive::ARCHIVE_GZIP;
      }

    switch($type)
      {
      case CmsArchive::ARCHIVE_TAR:
	$this->_obj = new tar_file($filename);
	break;
      case CmsArchive::ARCHIVE_GZIP:
	$this->_obj = new gzip_file($filename);
	break;
      case CmsArchive::ARCHIVE_BZIP:
	$this->_obj = new bzip_file($filename);
	break;
      default:
	throw new Exception('Invalid archive type');
      }

    $this->set_compression(5);

  }

  
  /**
   * Set options for the creation or extraction of the archive
   *
   * @param array array of options
   * @return void
   **/
  protected function set_options($options)
  {
    $this->_obj->set_options($options);
  }


  /**
   * Set the base directory for operations
   *
   * @param string Directory for add-file and extract operations
   * @return void
   **/
  public function set_basedir($basedir='.')
  {
    $tmp = array('basedir'=>$basedir);
    $this->_obj->set_options($tmp);
  }

  
  /**
   * Set the name of the archive to be read or written
   *
   * @param string File specification to the archive
   * @return void
   **/
  public function set_name($filespec)
  {
    $tmp = array('name'=>$filespec);
    $this->_obj->set_options($tmp);
  }


  /**
   * Set the path that is prepended to every filename in the archive
   *
   * @param string Path to prepend
   * @return void
   **/
  public function set_prepend($path)
  {
    $tmp = array('prepend'=>$path);
    $this->_obj->set_options($tmp);
  }


  /**
   * Indicate wether or not to create/extract archive in memory
   *
   * @param bool flag
   * @return void
   **/
  public function set_inmemory($flag=true)
  {
    $flag = ($flag === true)?1:0;
    $tmp = array('inmemory'=>$flag);
    $this->_obj->set_options($tmp);
  }


  /**
   * Set overwrite flag
   * This flag indicates wether or not to overwrite existing files when
   * creating or extracting archives.
   *
   * @param bool flag
   * @return void
   **/
  public function set_overwrite($flag=true)
  {
    $flag = ($flag === true)?1:0;
    $tmp = array('overwrite'=>$flag);
    $this->_obj->set_options($tmp);
  }


  /**
   * Set recursion flag
   *
   * @param bool flag
   * @return void
   **/
  public function set_recursive($flag=true)
  {
    $flag = ($flag === true)?1:0;
    $tmp = array('recurse'=>$flag);
    $this->_obj->set_options($tmp);
  }


  /**
   * Set store paths flag
   * Indicates wether paths should be stored in the archive or not.
   *
   * @param bool flag
   * @return void
   **/
  public function set_storepaths($flag=true)
  {
    $flag = ($flag === true)?1:0;
    $tmp = array('storepaths'=>$flag);
    $this->_obj->set_options($tmp);
  }


  /**
   * Set follow symbolic links flag
   *
   * @param bool flag
   * @return void
   **/
  public function set_followlinks($flag=true)
  {
    $flag = ($flag === true)?1:0;
    $tmp = array('followlinks'=>$flag);
    $this->_obj->set_options($tmp);
  }


  /**
   * Set compression level
   * a value between 0 and 9, where 0 is no compression.
   *
   * @param int Compression level
   * @return void
   **/
  public function set_compression($level=5)
  {
    $level = min($level,9);
    $level = max(0,$level);
    $tmp = array('level'=>$level);
    $this->_obj->set_options($tmp);
  }


  /**
   * Add a single file to the archive
   *
   * @param string File name
   * @return void
   **/
  public function add_file($filename)
  {
    if( file_exists($filename) )
      {
	$this->_obj->add_files(array($filename));
      }
  }


  /**
   * Exclude files from the archive
   *
   * @param array File specification list (supports wildcards)
   * @return void
   **/
  function exclude_files($files)
  {
    $this->_obj->exclude_files($files);
  }


  /**
   * Add multiple files to the archive
   * This method supports wildcarts in paths like 'path/*.jpg'
   * 
   * @params array Array of strings containing file specifications
   * @return void
   **/
  public function add_files($files)
  {
    $this->_obj->add_files($files);
  }


  /**
   * Create the archive file
   *
   * @return void
   **/
  public function create()
  {
    $this->exclude_files_by_pattern();
    $this->_obj->create_archive();
  }


  /**
   * Extract the specified files from the archive
   *
   * @return void
   **/
  public function extract()
  {
    $this->exclude_files_by_pattern();
    $this->_obj->extract_files();
  }


  /**
   * Retrieve the list of errors (if any)
   *
   * @return array Array of strings containing error messages
   **/
  public function get_errors()
  {
    return $this->_obj->errors();
  }


  public function set_exclude_patterns($patterns)
  {
    $this->_excludes = $patterns;
  }


  private function exclude_files_by_pattern()
  {
    if( !is_array($this->_excludes) ) return;
    if( !count($this->_excludes) ) return;

    $tmp = array();
    foreach( $this->_obj->files as $file )
      {
	$bad = false;
	foreach( $this->_excludes as $excl )
	  {
	    if( @preg_match( "/".$excl."/i", $file['name'] ) )
	      {
		$bad = true;
		break;
	      }
	  }
	if( !$bad )
	  {
	    $tmp[] = $file;
	  }
      }
    $this->_obj->files = $tmp;
  }
} // end of class
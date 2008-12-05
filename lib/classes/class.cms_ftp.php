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
 * Class to provide ftp client functionality
 *
 * @author Robert Campbell
 * @since 2.0
 * @version $Revision$
 * @modifiedby $LastChangedBy$
 * @lastmodified $Date$
 * @license GPL
 **/
class CmsFtp extends CmsObject
{
  private var $_ftp; // ftp class object

  public function __construct()
  {
    parent::__construct();
    require_once(cms_join_path(CmsConfig::get('root_path').'/lib/ftp/ftp_class.php'));
    $this->_ftp = new ftp(TRUE);
	$this->_ftp->LocalEcho = FALSE;
  }


  /**
   * Set verbose mode
   *
   * @param bool The verbosity status
   * @return void
   **/
  public function set_verbose($flag=TRUE)
  {
    if( $flag ) $flag = TRUE; else $flag = FALSE;
    $this->_ftp->Verbose = $flag;
  }


  /**
   * Get ftp communication messages
   *
   * @return array An Array of message strings
   **/
  public function get_messages()
  {
	  // return array of strings
	  if( !is_array($this->_ftp->_messages) ) return FALSE;
	  if( !count($this->_ftp->_messages) ) return FALSE;
	  return $this->_ftp->_messages;
  }


  /**
   * Get the latest error
   *
   * @return string The latest error message
   **/
  public function pop_error()
  {
	  $tmp = $this->_ftp->PopError();
	  if( $tmp === false ) $tmp = FALSE;
	  return $tmp;
  }


  /**
   * Set local echo
   *
   * @param bool use FALSE to turn off local echo
   * @return void
   **/
  public function set_local_echo($flag=TRUE)
  {
    if( $flag ) $flag = TRUE; else $flag = FALSE;
    $this->_ftp->LocalEcho = $flag;
  }

  /**
   * Set the connection details
   *
   * @param string Hostname or IP address
   * @param int    Port number to connect to
   * @param bool   Reconnect automatically if connection fails
   * @return bool
   **/
  public function set_server($server,$port,$reconnect=true)
  {
	  $port = intval($port);
	  return $this->_ftp->SetServer($server,$port,$reconnect);
  }


  /**
   * Connect to a server
   * if server is not specified, connect using the details provided
   * via the set_server method.
   *
   * @param string Hostname or IP address
   * @return bool
   **/
  public function connect($server=NULL)
  {
    return $this->_ftp->connect($server);
  }


  /**
   * Login to the ftp server
   *
   * @param string Account username
   * @param string Account password
   * @return bool
   **/
  public function login($username,$password)
  {
    return $this->_ftp->login($username,$password);
  }


  /**
   * Set file transfer type
   *
   * @param int FileType (1=ASCII, 2=BINARY, 3=AUTO)
   * @return bool
   **/
  public function set_type($type)
  {
	  $tmp = '';
	  switch($type)
		  {
		  case 1:
			  $tmp = FTP_ASCII;
			  break;
		  case 2:
			  $tmp = FTP_BINARY;
			  break;
		  case 3:
			  $tmp = FTP_AUTOASCII;
			  break;
		  default:
			  return FALSE;
		  }
	  return $this->_ftp->SetType($tmp);
  }


  /**
   * Set passive mode
   *
   * @param bool Flag for passive mode
   * @return bool
   **/
  public function set_passive($flag = TRUE)
  {
	  // todo
	  if( $flag ) $flag = TRUE; else $flag = FALSE;
	  return $this->_ftp->Passive($flag);
  }


  /**
   * Disconnect from the ftp server
   *
   * @param bool Force disconnection
   * @return bool
   **/
  public function quit($flag = false)
  {
    if( $flag ) $flag = true; else $flag = false;
    return $this->_ftp->quit($flag);
  }


  /**
   * Set umask for uploaded files
   * 
   * @param integer Octal description of umask
   * @return bool
   **/
  public function set_umask($umask=0022)
  {
    return $this->_ftp->SetUmask($umask);
  }


  /**
   * Set ftp client timeout (in seconds)
   *
   * @param int Number of seconds before timeout occurs
   * @return bool
   **/
  public function set_timeout($timeout=30)
  {
	  return $this->_ftp->SetTimeout($timeout);
  }


  /**
   * Return the current working directory
   *
   * @return string
   **/
  public function pwd()
  {
	  $tmp = $this->_ftp->pwd();
	  if( $tmp === true ) $tmp = TRUE;
	  return $tmp;
  }


  /**
   * Change directories up one level
   *
   * @return bool
   **/
  public function cdup()
  {
	  $tmp = $this->_ftp->cdup();
	  if( $tmp === true ) $tmp = TRUE;
	  return $tmp;
  }


  /**
   * Change working directory
   *
   * @param string Directory to change to
   * @return bool
   **/
  public function chdir($dir)
  {
    return $this->_ftp->chdir($dir);
  }


  /**
   * Remove the named directory
   *
   * @param string Directory to remove
   * @return bool
   **/
  public function rmdir($dir)
  {
	  return $this->_ftp->rmdir();
  }


  /**
   * Create the named directory
   *
   * @param string Directory to create
   * @return bool
   **/
  public function mkdir($dir)
  {
	  return $this->_ftp->mkdir();
  }


  /**
   * Rename a file or directory
   *
   * @param string Name of source file or directory
   * @param string Destination name
   * @return bool
   **/
  public function rename($from,$to)
  {
	  return $this->_ftp->rename($from,$to);
  }


  /**
   * Return the size of a file 
   *
   * @param string the name of the file
   * @return integer or FALSE;
   **/
  public function filesize($pathname)
  {
	  return $this->_ftp->filesize($pathname);
  }


  /**
   * Abort the current operation
   *
   * @return bool
   **/
  public function abort()
  {
	  $tmp = $this->_ftp->abort();
	  if( $tmp === true ) $tmp = TRUE;
	  return $tmp;
  }


  public function mdtm($pathname)
  {
	  return $this->_ftp->mdtm($pathname);
  }


  /**
   * Return the system type
   *
   * @return string
   **/
  public function systype() 
  {
	  return $this->_ftp->systype();
  }


  /**
   * Delete a file
   *
   * @param string name of the file
   * @return bool
   **/
  public function delete($pathaname)
  {
	  return $this->_ftp->delete($pathname);
  }


  /**
   * Execute a site command
   *
   * @param string Command
   * @param string Function
   * @return bool
   **/
  public function site($command,$fnction="site")
  {
	  return $this->_ftp->site($command,$fnction);
  }


  /**
   * Change the permissions of the specified file or directory
   *
   * @param string Name of the file or directory
   * @param integer Octal representation of the permission
   * @return bool
   **/
  public function chmod($pathname,$mode)
  {
	  return $this->_ftp->chmod($pathname,$mode);
  }

  /**
   * Perform ftp client restore command
   *
   * @return bool
   **/
  public function restore($from)
  {
	  return $this->_ftp->restore($from);
  }


  /**
   * Retrieve a list of the ftp server features
   *
   * @return array Array of strings representing features
   **/
  public function features()
  {
	  $tmp = $this->_ftp->features();
	  if( $tmp === true ) 
		  {
			  return $this->_ftp->_features;
		  }
	  return $tmp;
  }


  public function rawlist($pathname='',$arg='')
  {
	  return $this->_ftp->rawlist($pathname,$arg);
  }

  public function nlist($pathname='')
  {
	  return $this->_ftp->nlist($pathname);
  }


  /**
   * Test if a file exists
   * This is an alias for the file_exists() method
   *
   * @param string Path to the file
   * @return bool
   **/
  public function exists($pathname)
  {
	  return file_exists($pathname);
  }


  /**
   * Test if a file exists
   *
   * @param string Path to the file
   * @return bool
   **/
  public function file_exists($pathname)
  {
	  $tmp = $this->_ftp->file_exists($pathname);
	  if( $tmp === true ) $tmp = TRUE;
	  return $tmp;
  }


  /**
   * Download a file from the ftp server
   * If the ftp server supports it, and the restore_position is greater than 0
   * an attempt will be made to restore the file from the specified position
   *
   * @params string Remote filename
   * @params string Local file name
   * @params int    Position in file to restore from.
   **/
  public function get($remotefile,$localfile=NULL,$restore_position=0)
  {
	  $tmp = $this->_ftp->get($remotefile,$localfile,$rest);
	  if( $tmp === true ) $tmp = TRUE;
	  return $tmp;
  }


  /**
   * Upload a file to the ftp server
   * If the ftp server supports it, and the restore position is greater than 0
   * an attempt will be made to restore the file from the speicified position.
   *
   * @params string Local file specification
   * @params string Remote filename (if null name of local file will be used)
   * @params int    Position in file to restore from.
   **/
  public function put($localfile,$remotefile=NULL,$rest=0)
  {
	  $tmp = $this->_ftp->put($localfile,$remotefile,$rest);
	  if( $tmp === true ) $tmp = TRUE;
	  return $tmp;
  }


  /**
   * Upload multiple files to the ftp server
   *
   * @param string Directory name (or filemask) on local host
   * @param string Remote destination
   * @param bool   Flag indicating wether to upload all files before returning.
   * @return bool
   **/
  public function mput($local='.',$remote=NULL,$continuous=false)
  {
	  $tmp = $this->_ftp->mput($local,$remote,$continuous);
	  if( $tmp === true ) $tmp = TRUE;
	  return $tmp;
  }


  /**
   * Download multiple files from the ftp server
   *
   * @param string Directory name on remote server (or filemask)
   * @param string Directory name on local server
   * @param bool   Flag indicating wether to download all files before returning.
   * @return bool
   **/
  public function mget($remote,$local='.',$continuous=false)
  {
	  $tmp = $this->_ftp->mget($remote,$local,$continuous);
	  if( $tmp === true ) $tmp = TRUE;
	  return $tmp;
  }


  /**
   * Delete multiple files from ftp server
   *
   * @param string Directory name or filemask on remote server
   * @param bool   Flag indicating wether to delete all files before returning.
   * @return bool
   **/
  public function mdel($remote,$continuous=false)
  {
	  $tmp = $this->_ftp->mdel($remote,$continuous);
	  if( $tmp === true ) $tmp = TRUE;
	  return $tmp;
  }

  /**
   * Create a directory path on the remote server.
   * 
   * @param string Complete path to create
   * @param int    Octal representation of the permissions for each created directory.
   * @return bool
   **/
  public function mmkdir($dir,$mode=0777)
  {
	  $tmp = $this->_ftp->mmkdir($dir,$mode);
	  if( $tmp === true ) $tmp = TRUE;
	  if( $tmp === false ) $tmp = FALSE;
	  return $tmp;
  }

} // end of class

# vim:ts=4 sw=4 noet
?>
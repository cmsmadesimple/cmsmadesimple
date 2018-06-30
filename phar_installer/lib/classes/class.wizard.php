<?php

namespace __appbase;

class wizard
{
  private static $_instance = null;
  private $_name = null;
  private $_stepvar = 's';
  private $_steps;
  private $_stepobj;
  private $_classdir;
  private $_namespace;
  private $_initialized;

  const STATUS_OK    = 'OK';
  const STATUS_ERROR = 'ERROR';
  const STATUS_BACK  = 'BACK';
  const STATUS_NEXT  = 'NEXT';

  private function __construct($classdir,$namespace)
  {
    $this->_namespace = $namespace;
    if( !is_dir($classdir) ) throw new \Exception('Could not find wizard steps in '.$classdir);

    $this->_classdir = $classdir;
    $this->_name = basename($classdir);

  }

  final public static function &get_instance($classdir = '', $namespace = '')
  {
    if( !self::$_instance ) self::$_instance = new wizard($classdir,$namespace);
    return self::$_instance;
  }

  private function _init()
  {
      if( $this->_initialized ) return;
      $this->_initialized = true;

      // find all of the classes in the wizard directory.
      $di = new \DirectoryIterator($this->_classdir);
      $ri = new \RegexIterator($di,'/^class\.wizard.*\.php$/');
      $files = array();
      foreach( $ri as $one ) {
          $files[] = $one->getFilename();
      }
      if( !count($files) ) throw new \Exception('Could not find wizard steps in '.$classdir);
      sort($files);

      $_data = array();
      for( $i = 0; $i < count($files); $i++ ) {
          $idx = $i+1;
          $filename = $files[$i];
          $classname = substr($filename,6,strlen($filename)-10);
          $rec = array('fn'=>$filename,'class'=>'','name'=>'','description'=>'','active'=>'');
          $fullclass = $classname;

          if( $this->_namespace ) $fullclass = $this->_namespace.'\\'.$classname;
          $rec['classname'] = $classname;
          $rec['class'] = $fullclass;
          $rec['active'] = ($idx == $this->cur_step())?1:0;
          $_data[$idx] = $rec;
      }
      $this->_steps = $_data;
  }

  final public function get_nav()
  {
    $this->_init();
    return $this->_steps;
  }

  final public function get_step_var()
  {
    return $this->_stepvar;
  }

  final public function set_step_var($str)
  {
    if( $str ) $this->_stepvar = $str;
  }

  final public function cur_step()
  {
    $val = 1;
    if( $this->_stepvar && isset($_GET[$this->_stepvar]) ) $val = (int)$_GET[$this->_stepvar];
    return $val;
  }

  final public function finished()
  {
    $this->_init();
    return $this->cur_step() > $this->num_steps();
  }

  final public function num_steps()
  {
    $this->_init();
    return count($this->_steps);
  }

  final public function &get_step()
  {
    $this->_init();
    if( is_object($this->_stepobj) ) return $this->_stepobj;

    $rec = $this->_steps[$this->cur_step()];
    if( isset($rec['class']) && class_exists($rec['class']) ) {
      $obj = new $rec['class'];
      if( is_object($obj) ) {
	$this->_stepobj = $obj;
	return $obj;
      }
    }
  }

  public function get_data($key,$dflt = null)
  {
      $sess = session::get();
      if( !isset($sess[$key]) ) return $dflt;
      return $sess[$key];
  }

  public function set_data($key,$value)
  {
      $sess = session::get();
      $sess[$key] = $value;
  }

  public function clear_data($key)
  {
      $sess = session::get();
      if( isset($sess[$key]) ) unset($sess[$key]);
  }

  public function process()
  {
      $this->_init();
      $res = $this->get_step()->run();
      return $res;
  }

  final public function step_url($idx)
  {
      $this->_init();

      // get the url to the specified step index
      $idx = (int)$idx;
      if( $idx < 1 || $idx > $this->num_steps() ) return;

      $request = request::get();
      $url = $request->raw_server('REQUEST_URI');
      $urlmain = explode('?',$url);

      parse_str($url,$parts);
      $parts[$this->_stepvar] = $idx;

      $tmp = array();
      foreach( $parts as $k => $v ) {
          $tmp[] = $k.'='.$v;
      }
      $url = $urlmain[0].'?'.implode('&',$tmp);
      return $url;
  }

  final public function next_url()
  {
      $this->_init();
      $request = request::get();
      $url = $request->raw_server('REQUEST_URI');
      $urlmain = explode('?',$url);

      $parts = parse_str($url,$parts);
      $parts[$this->_stepvar] = $this->cur_step() + 1;
      if( $parts[$this->_stepvar] > $this->num_steps() ) return;

      $tmp = array();
      foreach( $parts as $k => $v ) {
          $tmp[] = $k.'='.$v;
      }
      $url = $urlmain[0].'?'.implode('&',$tmp);
      return $url;
  }

  final public function prev_url()
  {
      $this->_init();
      $request = request::get();
      $url = $request->raw_server('REQUEST_URI');
      $urlmain = explode('?',$url);

      parse_str($url,$parts);
      $parts[$this->_stepvar] = $this->cur_step() - 1;
      if( $parts[$this->_stepvar] <= 0 ) return;

      $tmp = array();
      if( count($parts) ) {
          foreach( $parts as $k => $v ) {
              $tmp[] = $k.'='.$v;
          }
      }
      $url = $urlmain[0].'?'.implode('&',$tmp);
      return $url;
  }

} // end of class
?>

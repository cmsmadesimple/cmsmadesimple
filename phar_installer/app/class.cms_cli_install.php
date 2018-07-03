<?php
namespace cms_autoinstaller;
use \__appbase\utils;

include_once(__DIR__.'/lib/compat.functions.php');
include_once(__DIR__.'/lib/class.cms_install_base.php');

class cms_cli_install extends cms_install_base
{
    private $_config = ['tmpdir'=>null,'op'=>'null','dofiles'=>true,'interactive'=>true,'dest'=>null];
    private $_dest_version;
    private $_dest_name;
    private $_dest_schema;

    public function __construct()
    {
        parent::__construct( __FILE__ );
        // test if we in a phar or not.
        $this->load_config();
        $this->load_version_details();
    }

    protected function load_version_details()
    {
        $verfile = dirname($this->get_archive()).'/version.php';
        if( !is_file( $verfile ) ) throw new \Exception( 'Could not find version file 2');
        include_once( $verfile );
        $this->_dest_version = $CMS_VERSION;
        $this->_dest_name = $CMS_VERSION_NAME;
        $this->_dest_schema = $CMS_SCHEMA_VERSION;
    }

    protected function load_config()
    {
        static $_loaded = false;

        if( $_loaded ) return;
        $_loaded = true;

        $this->_config['dest'] = realpath( getcwd() );

        // parse the arguments
        $opts = getopt('nhf:o:',[ 'nofiles', 'tmpdir:','op:','dest:','help']);
        if( isset( $opts['h'] ) || isset($opts['help']) ) {
            $this->show_help();
            return;
        }

        if( isset( $opts['f']) ) {
            $file = $opts['f'];
            if( is_file( $file ) ) {
                $config = parse_ini_file( $file );
                if( $config ) {
                    $this->_config = $config;
                }
            }
        }

        foreach( $opts as $key => $val ) {
            switch( $key ) {
            case 'n':
                $this->_config['interactive'] = false;
                break;

            case 'tmpdir':
                $this->_config['tmpdir'] = $val;
                break;

            case 'dest':
                $this->_config['dest'] = realpath($val);
                break;

            case 'nofiles':
                $this->_config['dofiles'] = false;
                break;

            case 'o':
            case 'op':
                $this->_config['op'] = strtolower( $val );
                break;
            }
        }
    }

    public function get_destdir() {
        return $this->_config['dest'];
    }

    public function get_archive() {
        $archive = ( isset($this->_config['archive']) ) ? $this->_config['archive'] : 'data/data.tar.gz';
        $archive = dirname($this->get_appdir()).'/'.$archive;
        return $archive;
    }

    public function get_dest_version() {
        return $this->_dest_version;
    }

    public function get_dest_name() {
        return $this->_dest_name;
    }

    public function get_dest_schema() {
        return $this->_dest_schema;
    }

    public function get_op()
    {
        if( !$this->_config['op'] ) return 'install';
        return $this->_config['op'];
    }

    public function is_interactive()
    {
        return $this->_config['interactive'];
    }

    public function get_options()
    {
        return $this->_config;
    }

    public function set_op( $op )
    {
        $this->_config['op'] = trim($op);
    }

    public function merge_options( $params )
    {
        if( is_array( $params ) && count($params ) ) {
            $this->_config = array_merge( $this->_config, $params );
        }
    }

    protected function get_steps()
    {
        $dir = $this->get_appdir().'/cli';
        $files = glob( $dir.'/class.step_*.php' );
        if( !$files ) return;

        $out = null;
        foreach( $files as $one ) {
            $bn = basename( $one );
            $class = substr( $bn, 6 );
            $class = substr( $class, 0, -4 );
            $classname = '\\cms_autoinstaller\\cli_step\\'.$class;
            $out[] = [ $one, $classname ];
        }
        sort( $out );
        return $out;
    }

    public function run()
    {
        \__appbase\translator()->set_default_language('en_US');

        try {
            // get all of the tasks, and run them in sequence
            $tasks = $this->get_steps();
            foreach( $tasks as $task ) {
                require_once( $task[0] );
                $task_class = $task[1];
                $task = new $task_class( $this );
                $task->run();
            }
        }
        catch( user_aborted $e ) {
            exit(0);
        }
        catch( \Exception $e ) {
            fprintf(STDERR,"ERROR: ".$e->GetMessage()."\n");
            exit(1);
        }
    }
} // class

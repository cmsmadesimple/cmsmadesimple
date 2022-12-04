<?php

namespace cms_autoinstaller;

class manifest_reader
{
    private $_filename;
    private $_compressed;
    private $_generated;
    private $_from_version;
    private $_from_name;
    private $_to_version;
    private $_to_name;
    private $_has_read = false;
    private $_added = array();
    private $_changed = array();
    private $_deleted = array();

    public function __construct($dir)
    {
        if( !is_dir($dir) ) throw new \Exception(\__appbase\lang('error_internal','mr100'));
        $fn = "$dir/MANIFEST.DAT.gz";
        if( file_exists($fn) ) {
            $this->_filename = $fn;
            $this->_compressed = true;
        }
        else {
            $fn = "$dir/MANIFEST.DAT";
            if( file_exists($fn) ) {
                $this->_filename = $fn;
                $this->_compressed = false;
            }
            else {
                throw new \Exception(\__appbase\lang('error_internal','mr101'));
            }
        }
    }

    protected function handle_header($line)
    {
        $cols = explode(':',$line);
        foreach( $cols as &$col ) {
            $col = trim($col);
        }
        if( count($cols) != 2 ) throw new \Exception(\__appbase\lang('error_internal','mr105'));

        switch( $cols[0] ) {
        case 'MANIFEST_GENERATED':
            $this->_generated = (int)$cols[1];
            break;
        case 'MANIFEST FROM VERSION':
            $this->_from_version = $cols[1];
            break;
        case 'MANIFEST FROM NAME':
            $this->_from_name = $cols[1];
            break;
        case 'MANIFEST TO VERSION':
            $this->_to_version = $cols[1];
            break;
        case 'MANIFEST TO NAME':
            $this->_to_name = $cols[1];
            break;
        }
    }

    protected function handle_added($fields)
    {
        $this->_added[] = array('filename'=>$fields[2],'checksum'=>$fields[1]);
    }

    protected function handle_changed($fields)
    {
        $this->_changed[] = array('filename'=>$fields[2],'checksum'=>$fields[1]);
    }

    protected function handle_deleted($fields)
    {
        $this->_deleted[] = array('filename'=>$fields[2],'checksum'=>$fields[1]);
    }

    protected function handle_line($line)
    {
        if( !$line ) return;
        if( \__appbase\startswith($line,'MANIFEST') ) return $this->handle_header($line);

        $fields = explode(' :: ',$line);
        if( count($fields) != 3 ) throw new \Exception(\__appbase\lang('error_internal','mr103'));

        switch( $fields[0] ) {
        case 'ADDED':
            return $this->handle_added($fields);
            break;
        case 'CHANGED':
            return $this->handle_changed($fields);
            break;
        case 'DELETED':
            return $this->handle_deleted($fields);
            break;
        default:
            throw new \Exception(\__appbase\lang('error_internal','mr104'));
        }
    }

    protected function read()
    {
        if( !$this->_has_read ) {
            $fopen = $fclose = $fgets = $feof = null;
            if( $this->_compressed ) {
                $fopen = 'gzopen';
                $fclose = 'gzclose';
                $fgets = 'gzgets';
                $feof = 'gzeof';
            }
            else {
                $fopen = 'fopen';
                $fclose = 'fclose';
                $fgets = 'fgets';
                $feof = 'feof';
            }

            // copy the manifest file to a temporary location
            $tmpdir = \__appbase\get_app()->get_tmpdir();
            $tmpname = tempnam($tmpdir,'man');
            @copy($this->_filename,$tmpname);
            $fh = $fopen($tmpname,'r');
            if( !$fh )  {
              echo "DEBUG: $fopen on ".$this->_filename."<br/>"; die();
              throw new \Exception(\__appbase\lang('error_internal','mr102'));
            }
            while( !$feof($fh) ) {
                $line = $fgets($fh);
                $line = trim($line);
                $this->handle_line($line);
            }
            $fclose($fh);
            $this->_has_read = true;
        }
    }

    public function get_generated()
    {
        $this->read();
        return $this->_generated;
    }

    public function to_version()
    {
        $this->read();
        return $this->_to_version;
    }

    public function to_name()
    {
        $this->read();
        return $this->_to_name;
    }

    public function from_version()
    {
        $this->read();
        return $this->_from_version;
    }

    public function from_name()
    {
        $this->read();
        return $this->_from_name;
    }

    public function get_added()
    {
        $this->read();
        return $this->_added;
    }

    public function get_changed()
    {
        $this->read();
        return $this->_changed;
    }

    public function get_deleted()
    {
        $this->read();
        return $this->_deleted;
    }

} // end of class
?>

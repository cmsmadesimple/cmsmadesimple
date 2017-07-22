<?php
namespace ModuleManager;

class operations
{
    private $_mod;
    const MODULE_DTD_VERSION = '1.3';

	/**
	 * @ignore
	 */
	private $xml_exclude_files = array('^\.svn' , '^CVS$' , '^\#.*\#$' , '~$', '\.bak$', '^\.git', '^\.tmp$');

	/**
	 * @ignore
	 */
	private $xmldtd = '
<!DOCTYPE module [
  <!ELEMENT module (dtdversion,name,version,description*,help*,about*,requires*,file+)>
  <!ELEMENT dtdversion (#PCDATA)>
  <!ELEMENT name (#PCDATA)>
  <!ELEMENT version (#PCDATA)>
  <!ELEMENT mincmsversion (#PCDATA)>
  <!ELEMENT description (#PCDATA)>
  <!ELEMENT help (#PCDATA)>
  <!ELEMENT about (#PCDATA)>
  <!ELEMENT requires (requiredname,requiredversion)>
  <!ELEMENT requiredname (#PCDATA)>
  <!ELEMENT requiredversion (#PCDATA)>
  <!ELEMENT file (filename,isdir,data)>
  <!ELEMENT filename (#PCDATA)>
  <!ELEMENT isdir (#PCDATA)>
  <!ELEMENT data (#PCDATA)>
]>';

    public function __construct( \ModuleManager $mod )
    {
        $this->_mod = $mod;
    }

    /**
     * Unpackage a module from an xml string
     * does not touch the database
     *
     * @internal
     * @param string $xmlurl The xml data for the package
     * @param bool $overwrite Should we overwrite files if they exist?
     * @param bool $brief If set to true, less checking is done and no errors are returned
     * @return array A hash of details about the installed module
     */
    function expand_xml_package( $xmluri, $overwrite = 0, $brief = 0 )
    {
        // first make sure that we can actually write to the module directory
        $dir = CMS_ASSETS_PATH.'/modules';
        if( !is_writable( $dir ) && $brief == 0 ) throw new \CmsFileSystemException(lang('errordirectorynotwritable'));

        $modops = \ModuleOperations::get_instance();
        $reader = new \XMLReader();
        $ret = $reader->open($xmluri);
        if( $ret == 0 ) throw new \CmsInvalidDataException($this->_mod->Lang('err_xml_open'));

        $havedtdversion = false;
        $moduledetails = [];
        if( is_file($xmluri) )	$moduledetails['size'] = filesize($xmluri);
        $required = array();
        while( $reader->read() ) {
            switch($reader->nodeType) {
			case \XMLReader::ELEMENT:
				switch( strtoupper($reader->localName) ) {
                case 'NAME':
                    $reader->read();
                    $moduledetails['name'] = $reader->value;
                    $loaded = $modops->GetLoadedModules();
                    // check if this module is already installed
                    if( isset( $loaded[$moduledetails['name']] ) && $overwrite == 0 && $brief == 0 ) {
                        throw new \CmsLogicException($this->_mod->Lang('err_xml_moduleinstalled'));
                    }
                    break;

                case 'DTDVERSION':
                    $reader->read();
                    if( $reader->value != self::MODULE_DTD_VERSION ) throw new \CmsInvalidDataException($this->_mod->Lang('err_xml_dtdmismatch'));
                    $havedtdversion = true;
                    break;

                case 'VERSION':
                    $reader->read();
                    $moduledetails['version'] = $reader->value;
                    $tmpinst = $modops->get_module_instance($moduledetails['name']);
                    if( $tmpinst && $brief == 0 ) {
                        $version = $tmpinst->GetVersion();
                        if( version_compare($moduledetails['version'],$version) < 0 ) {
                            throw new \RuntimeException($this->_mod->Lang('err_xml_oldermodule'));
                        }
                        else if (version_compare($moduledetails['version'],$version) == 0 && $overwrite == 0 ) {
                            throw new \RuntimeException($this->_mod->Lang('err_xml_sameversion'));
                        }
                    }
                    break;

                case 'MINCMSVERSION':
                    $name = $reader->localName;
                    $reader->read();
                    if( version_compare(CMS_VERSION,$reader->value) < 0 ) {
                        throw new \CmsLogicException($this->_mod->Lang('err_xml_moduleincompatible'));
                    }
                    $moduledetails[$name] = $reader->value;
                    break;

                case 'MAXCMSVERSION':
                case 'DESCRIPTION':
                case 'FILENAME':
                case 'ISDIR':
                    $name = $reader->localName;
                    $reader->read();
                    $moduledetails[$name] = $reader->value;
                    break;

                case 'HELP':
                case 'ABOUT':
                    $name = $reader->localName;
                    $reader->read();
                    $moduledetails[$name] = base64_decode($reader->value);
                    break;

                case 'REQUIREDNAME':
                    $reader->read();
                    $requires['name'] = $reader->value;
                    break;

                case 'REQUIREDVERSION':
                    $reader->read();
                    $requires['version'] = $reader->value;
                    break;

                case 'DATA':
                    $reader->read();
                    $moduledetails['filedata'] = $reader->value;
                    break;
				}
				break;

			case \XMLReader::END_ELEMENT:
				switch( strtoupper($reader->localName) ) {
                case 'REQUIRES':
                    if( count($requires) != 2 ) continue;
                    if( !isset( $moduledetails['requires'] ) ) $moduledetails['requires'] = array();
                    $moduledetails['requires'][] = $requires;
                    $requires = array();
                    break;

                case 'FILE':
                    if( $brief != 0 ) continue;

                    // finished a first file
                    if( !isset( $moduledetails['name'] ) || !isset( $moduledetails['version'] ) ||
                        !isset( $moduledetails['filename'] ) || !isset( $moduledetails['isdir'] ) ) {
                        throw new \CmsInvalidDataException($this->Lang('err_xml_invalid'));
                        return false;
                    }

                    // ready to go
                    $moduledir=$dir.DIRECTORY_SEPARATOR.$moduledetails['name'];
                    $filename=$moduledir.$moduledetails['filename'];
                    if( !file_exists( $moduledir ) ) {
                        if( !@mkdir( $moduledir ) && !is_dir( $moduledir ) ) {
                            throw new \CmsFileSystemException(lang('errorcantcreatefile').': '.$moduledir);
                            break;
                        }
                    }
                    else if( $moduledetails['isdir'] ) {
                        if( !@mkdir( $filename ) && !is_dir( $filename ) ) {
                            throw new \CmsFileSystemException(lang('errorcantcreatefile').': '.$filename);
                            break;
                        }
                    }
                    else {
                        $data = $moduledetails['filedata'];
                        if( strlen( $data ) ) $data = base64_decode( $data );
                        $fp = @fopen( $filename, "w" );
                        if( !$fp ) throw new \CmsFileSystemException(lang('errorcantcreatefile').' '.$filename);
                        if( strlen( $data ) ) @fwrite( $fp, $data );
                        @fclose( $fp );
                    }
                    unset( $moduledetails['filedata'] );
                    unset( $moduledetails['filename'] );
                    unset( $moduledetails['isdir'] );
                    break;
				}
				break;
            }
        } // while

        $reader->close();
        if( $havedtdversion == false ) throw new \CmsInvalidDataException($this->_mod->Lang('err_xml_dtdmismatch'));

        // we've created the module's directory
        unset( $moduledetails['filedata'] );
        unset( $moduledetails['filename'] );
        unset( $moduledetails['isdir'] );

        if( !$brief ) audit('','Module', 'Expanded module: '.$moduledetails['name'].' version '.$moduledetails['version']);

        return $moduledetails;
    }

    function create_xml_package( \CMSModule $modinstance, &$message, &$filecount )
    {
        // get a file list
        $filecount = 0;
        $dir = $modinstance->GetModulePath();
        if( !is_directory_writable( $dir ) ) throw new \CmsFileSystemException(lang('errordirectorynotwritable'));

        // generate the moduleinfo.ini file
        \ModuleOperations::get_instance()->generate_moduleinfo($modinstance);
        $files = get_recursive_file_list( $dir, $this->xml_exclude_files );

        $xmltxt  = '<?xml version="1.0" encoding="ISO-8859-1"?>';
        $xmltxt .= $this->xmldtd."\n";
        $xmltxt .= "<module>\n";
        $xmltxt .= "	<dtdversion>".self::MODULE_DTD_VERSION."</dtdversion>\n";
        $xmltxt .= "	<name>".$modinstance->GetName()."</name>\n";
        $xmltxt .= "	<version>".$modinstance->GetVersion()."</version>\n";
        $xmltxt .= "    <mincmsversion>".$modinstance->MinimumCMSVersion()."</mincmsversion>\n";
        $xmltxt .= "	<help><![CDATA[".base64_encode($modinstance->GetHelpPage())."]]></help>\n";
        $xmltxt .= "	<about><![CDATA[".base64_encode($modinstance->GetAbout())."]]></about>\n";
        $desc = $modinstance->GetAdminDescription();
        if( $desc != '' ) $xmltxt .= "	<description><![CDATA[".$desc."]]></description>\n";

        $depends = $modinstance->GetDependencies();
        foreach( $depends as $key=>$val ) {
            $xmltxt .= "	<requires>\n";
            $xmltxt .= "	  <requiredname>$key</requiredname>\n";
            $xmltxt .= "	  <requiredversion>$val</requiredversion>\n";
            $xmltxt .= "	</requires>\n";
        }
        foreach( $files as $file ) {
            // strip off the beginning
            if (substr($file,0,strlen($dir)) == $dir) $file = substr($file,strlen($dir));
            if( $file == '' ) continue;

            $xmltxt .= "	<file>\n";
            $filespec = $dir.DIRECTORY_SEPARATOR.$file;
            $xmltxt .= "	  <filename>$file</filename>\n";
            if( @is_dir( $filespec ) ) {
                $xmltxt .= "	  <isdir>1</isdir>\n";
            }
            else {
                $xmltxt .= "	  <isdir>0</isdir>\n";
                $data = base64_encode(file_get_contents($filespec));
                $xmltxt .= "	  <data><![CDATA[".$data."]]></data>\n";
            }

            $xmltxt .= "	</file>\n";
            ++$filecount;
        }
        $xmltxt .= "</module>\n";
        $message = 'XML package of '.strlen($xmltxt).' bytes created for '.$modinstance->GetName();
        $message .= ' including '.$filecount.' files';
        return $xmltxt;
    }

} // end of class

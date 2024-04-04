<?php
#---------------------------------------------------------------------------------------------------
# Module: UserGuide
# Author: Chris Taylor
# Copyright: (C) 2024 Chris Taylor, chris@binnovative.co.uk
# Licence: GNU General Public License version 3
#          see /UserGuide/lang/LICENCE.txt or <http://www.gnu.org/licenses/>
#---------------------------------------------------------------------------------------------------

class UserGuideImporterExporter {

    private $modulename;
    private $mod;
    private $preferences;
    private $tables;
    private $_xml;
    private $xml_exclude_files = ['^\.svn' , '^CVS$' , '^\#.*\#$' , '~$', '\.bak$', '^\.git', '^\.tmp$'];

    #---------------------
    # Magic methods
    #---------------------
    public function __construct() {

        $this->modulename = 'UserGuide';
        $this->mod = cms_utils::get_module( $this->modulename );

        $this->preferences = [
            'customModuleName',
            'adminSection',
            'useSmarty',
            'imageFolder'
        ];

        $this->tables = [   // table_from => table_to
            'module_userguide' => 'module_userguide',
            'module_userguide2' => 'module_userguide'
        ];

    }


    #---------------------
    # public functions
    #---------------------

    public function export() 
    {
        $this->_create_xml();
        $this->_copyFromPreferences();
        $this->_copyFromDataBase();
        // $this->_copyFromTemplates();
        $this->_copyFilesFromFolder();
        $this->_output_xml();
    }



    /**
     *  import uploaded .xml file - allow for xml format change @ v1.3+
     *  @param string $filename - uploaded xml file
     *  @return bool - success/fail
     */
    public function import($filename = NULL) 
    {
        if ( empty($filename) || !file_exists($filename) ) return false; 

        $file = file_get_contents( $filename );
        $this->_xml = new SimpleXMLElement( $file );
        $canImportFrom = [$this->modulename, 'UserGuide2']; 

        if ( !in_array($this->_xml->module, $canImportFrom) || !isset($this->_xml->version) )
            return false; // check xml module & version values

        $xmlVersion = $this->_xml->version;
        if ( $this->_xml->module=='UserGuide2' && $xmlVersion < 1.3  ) { // 1.2 & before version xml
            $this->_import_xml_v1();

        } else { // v1.3+
            $this->_copyToPreferences();
            $this->_copyToDataBase();
            // $this->_copyToTemplates();
            $this->_copyFilesToFolder();
        }
        $query = new UserGuideQuery;
        $query->updatePositions();

        return true;
    }



    #---------------------
    # export functions
    #---------------------

    private function _create_xml() {
    //***********************************************************************************************
    //
    //
    //***********************************************************************************************
        global $CMS_VERSION;

        $baseXML = '<?xml version="1.0" encoding="UTF-8"?><modulecontent></modulecontent>';
        $this->_xml = new SimpleXMLElement( $baseXML );
        $this->_xml->addChild('module', $this->mod->GetName());
        $this->_xml->addChild('version', $this->mod->GetVersion());
        $this->_xml->addChild('cmsversion', $CMS_VERSION);
        $this->_xml->addChild('exportdate', date('Y-m-d H:i:s'));

    }



    private function _copyFromPreferences() {
    //***********************************************************************************************
    // add all specified preferences into _xml
    //***********************************************************************************************
        if (empty($this->preferences)) return;

        foreach($this->preferences as $pref) {
            $prefs[$pref] = $this->mod->GetPreference($pref, '');
        }
        $this->_xml->addChild( 'prefs', base64_encode( serialize($prefs) ) );
    }



    private function _copyFromDataBase() {
    //***********************************************************************************************
    // add all data from specified tables into _xml
    //***********************************************************************************************
        if (empty($this->tables)) return;

        $db = \cms_utils::get_db();
        $data = array();
        foreach($this->tables as $old => $new) {
            $sql = 'SELECT * FROM '.cms_db_prefix().$new;
            $data[$new] = $db->GetArray($sql);
        }
        $this->_xml->addChild( 'db', base64_encode( serialize($data) ) );
    }



    private function _copyFromTemplates() {
    //***********************************************************************************************
    //
    //***********************************************************************************************

        // not yet implemented
    }



    private function _copyFilesFromFolder() {
    //***********************************************************************************************
    // recursive copy of all files & folders below 'imageFolder' (if set)
    //    from class.moduleoperations.inc.php > CreateXMLPackage
    //***********************************************************************************************
        $filecount = 0;
        $uploads_path = CmsApp::get_instance()->GetConfig()['uploads_path'];
        $imageFolder = $this->mod->GetPreference('imageFolder', '');
        $dir = $uploads_path.'/'.$imageFolder;

        if ($imageFolder=='' || !is_dir( $dir ) ) return;

        $files = get_recursive_file_list( $dir, $this->xml_exclude_files );
        $xmlFiles = $this->_xml->addChild( 'files' );
        foreach( $files as $file ) {
            // strip off the beginning
            if (substr($file,0,strlen($dir)) == $dir) $file = substr($file,strlen($dir));
            if ( $file == '' ) continue;

            $xmlFile = $xmlFiles->addChild( 'file' );
            $filespec = $dir.DIRECTORY_SEPARATOR.$file;
            $xmlFile->addChild( 'filename', $file );
            if ( @is_dir( $filespec ) ) {
                $xmlFile->addChild( 'isdir', '1' );
            }
            else {
                $xmlFile->addChild( 'isdir', '0' );
                $data = base64_encode(file_get_contents($filespec));
                $xmlFile->addChild( 'data', $data );
            }

            ++$filecount;
        }

    }



    private function _output_xml() {
    //***********************************************************************************************
    //
    //
    //***********************************************************************************************
        // create filename
        $date = date('Y-m-d_H-i-s', time());
        $filename = 'UserGuide_Export_' . $date . '.xml';

        ob_end_clean();
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private',false);
        header('Content-Description: Export');
        header('Content-Description: File Transfer');
        header('Content-Type: application/force-download');
        header('Content-Disposition: attachment; filename='.$filename);
        header('Content-Type: text/xml; charset=utf-8');

        echo $this->_xml->asXML();
        exit();
    }



    #---------------------
    # import functions
    #---------------------

    private function _import_xml_v1() {
    //***********************************************************************************************
    // import original format of xml <= v1.2.1  (doesn't update preferences)
    //    fields - add all imported pages after exisiting pages. If Title exists already = add '-2'
    //***********************************************************************************************
        $db = \cms_utils::get_db();
        $sql = 'SELECT title FROM ' . cms_db_prefix() . 'module_userguide';
        $curTitles = $db->GetCol($sql);
        $sql = 'SELECT MAX(position) FROM ' . cms_db_prefix() . 'module_userguide';
        $nextPos = $db->GetOne($sql) + 1;

        foreach ($this->_xml->fields->field as $field) {
            $fieldTitle = (string) $field->title;
            if (in_array ($fieldTitle, $curTitles)) $fieldTitle .= '-2';
            $sql = 'INSERT INTO ' . cms_db_prefix() . 'module_userguide
                (title, position, active, content)
                VALUES (?,?,?,?)';
            $dbr = $db->Execute($sql, array(
                (string) $fieldTitle,
                (int) $nextPos++,
                (int) $field->active,
                (string) $field->content
            ));
        }
    }



    // private function _expand_xml() {
    // //***********************************************************************************************
    // //
    // //***********************************************************************************************

    // }



    private function _copyToPreferences() {
    //***********************************************************************************************
    // get all valid preferences from _xml & update
    //***********************************************************************************************
        if ( !isset($this->_xml->prefs) ) return;

        $prefs = unserialize( base64_decode($this->_xml->prefs) );

        foreach($prefs as $preference_name => $value) {
            if ( in_array($preference_name, $this->preferences))
                $this->mod->SetPreference($preference_name, $value);
        }

        // Touch menu cache files - core will refresh (v2.0+ )
        foreach ( glob(cms_join_path(TMP_CACHE_LOCATION, 'cache*.cms')) as $filename ) {
            touch( $filename, time() - 360000 );
        }

    }



    private function _copyToDataBase() {
    //***********************************************************************************************
    // get all valid database tables from _xml & update - will normally replace existing rows (id)
    //***********************************************************************************************
        if ( !isset($this->_xml->db) ) return;

        $data = unserialize( base64_decode($this->_xml->db) );

        $db = \cms_utils::get_db();
        foreach($data as $tablename => $tabledata) {
            if ( array_key_exists($tablename, $this->tables) ) { // valid tablename
                $to_table = $this->tables[$tablename];
                foreach ($tabledata as $row) {
                    $data_rows = $row;
                    unset($data_rows['id']);    // remove id
                    if ( isset($data_rows['position']) ) $data_rows['position'] = '10000'; // set to end
                    $fields = implode(',', array_keys($data_rows) );
                    $phs = implode(',', array_fill(0, count($data_rows), '?') );
                    $sql = 'INSERT INTO '.CMS_DB_PREFIX.$to_table.' ('.$fields.') VALUES ('.$phs.')';
                    $res = $db->Execute($sql, array_values($data_rows));
                }
            }
        }

    }



    private function _copyToTemplates() {
    //***********************************************************************************************
    //
    //***********************************************************************************************

        // not yet implemented
    }



    private function _copyFilesToFolder() {
    //***********************************************************************************************
    //
    //***********************************************************************************************
        if ( !isset($this->_xml->files) ) return;

        $uploads_path = CmsApp::get_instance()->GetConfig()['uploads_path'];
        // first make sure that we can actually write to the uploads folder
        if ( !is_writable( $uploads_path ) ) return false;

        $imageFolder = $this->mod->GetPreference('imageFolder', '');
        $dir = $uploads_path.'/'.$imageFolder;

        foreach ($this->_xml->files->file as $xmlFile) {
            if (!isset($xmlFile->filename) || !isset($xmlFile->isdir) ) return false;

            $filename = $dir.$xmlFile->filename;
            $isdir = (string) $xmlFile->isdir;
            if ( !file_exists( $dir ) ) {
                if (!@mkdir( $dir ) && !is_dir( $dir )) break;

            } elseif ( $isdir ) {
                if (!@mkdir( $filename ) && !is_dir( $filename )) break;

            } else {
                $data = (string) $xmlFile->data;
                if ( strlen( $data ) ) $data = base64_decode( $data );
                $fp = @fopen( $filename, "w" );
                if ( !$fp ) throw new CmsFileSystemException(lang('errorcantcreatefile').' '.$filename);
                if ( strlen( $data ) ) @fwrite( $fp, $data );
                @fclose( $fp );

            }
        }

    }



} // UserGuideImporterExporter



<?php
namespace AdminSearch\Slaves;
use AdminSearch;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class TextAssetSlave extends AbstractSlave
{
    private $mod;

    public function __construct(AdminSearch $mod)
    {
        $this->mod = $mod;
    }

    public function get_name()
    {
        return $this->mod->Lang('lbl_textasset_search');
    }

    public function get_description()
    {
        return $this->mod->Lang('desc_textasset_search');
    }

    protected function test_file(string $filename)
    {
        $out = null;
        $intext = $this->get_text();
        $fh = fopen($filename,'r');
        $subpath = substr($filename, strlen(CMS_ASSETS_PATH));
        while( !feof($fh) ) {
            $data = fread($fh, 64*1024);
            if( ($pos = strpos($data,$intext)) !== FALSE ) {
                // found the string, now we should try to rewind up to 50 bytes
                // so we can get a good text description, if possible. (todo)
                $start = max(0,$pos - 50);
                $end = min(strlen($data),$pos+50);
                $text = substr($data,$start,$end-$start);
                $text = htmlentities($text);
                $text = str_replace($intext,'<span class="search_oneresult">'.$intext.'</span>',$text);
                $text = str_replace("\r",'',$text);
                $text = str_replace("\n",'',$text);

                $out = ['title'=>$subpath, 'text'=>$text ];
                break;
            }
        }
        fclose($fh);
        return $out;
    }

    public function get_matches()
    {
        $out = [];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $assets_modules_path = CMS_ASSETS_PATH.'/modules';
        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator(CMS_ASSETS_PATH)) as $it) {
            if( $it->isDir() || !$it->isReadable() ) continue;
            $pathName = $it->getPathName();
            if( startswith($pathName, $assets_modules_path) ) continue;
            $filename = basename($pathName);
            if( startswith($filename,'_') || startswith($filename,'.') ) continue;

            // get the mime type
            $type = finfo_file($finfo, $pathName);
            if( !startswith($type,'text/') ) continue;

            // now can search the file
            $tmp = $this->test_file($pathName);
            if( $tmp ) $out[] = $tmp;
        }
        finfo_close($finfo);
        return $out;
    }
} // class
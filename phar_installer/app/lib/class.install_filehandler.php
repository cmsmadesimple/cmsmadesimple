<?php

namespace cms_autoinstaller;

class install_filehandler extends \cms_autoinstaller\filehandler
{
  public function handle_file($filespec,$srcspec,\PharFileInfo $fi)
  {
    if( $this->is_excluded($filespec) ) return;
    if( $this->is_langfile($filespec) ) {
      if( !$this->is_accepted_lang($filespec) ) return;
    }

    if( !$this->dir_exists($filespec) ) $this->create_directory($filespec);

    $destname = $this->get_destdir().$filespec;
    if( file_exists($destname) && !is_writable($destname) ) throw new \Exception(\__appbase\lang('error_overwrite',$filespec));

    $cksum = md5_file($srcspec);
    @copy($srcspec,$destname);
    $cksum2 = md5_file($destname);
    if( $cksum != $cksum2 ) throw new \Exception(\__appbase\lang('error_checksum',$filespec));

    $this->output_string(\__appbase\lang('file_installed',$filespec));
  }
}

?>

<?php


function ensure_tmp_dirs_exist() : bool
{
  $dirs = [
    TMP_TEMPLATES_C_LOCATION,
    TMP_CACHE_LOCATION
  ];

  foreach( $dirs as $dir )
  {
    if( !is_dir($dir) )
    {
      if( !mkdir($dir, 0777, TRUE) )
      {
        echo "Failed to create directory: $dir\n";

        continue;
      }
    }

    // Set permissions only when the directory is not already writable.
    // On some filesystems (e.g. WSL-mounted Windows drives under /mnt) chmod()
    // always fails even though the directory is writable, which produced a
    // spurious "Failed to set permissions" error. If the directory is already
    // writable there is nothing to fix and nothing to report.
    if( !is_writable($dir) && !@chmod($dir, 0777) )
    {
      error_log("Failed to set permissions on directory: $dir");
    }
  }

  if( !is_writable(TMP_TEMPLATES_C_LOCATION) || !is_writable(TMP_CACHE_LOCATION) )
  {
    echo '<html><title>Error</title></head><body>';
    echo '<p>The following directories must be writable by the web server:<br>';
    echo 'tmp/cache<br>';
    echo 'tmp/templates_c<br></p>';
    echo '<p>Please correct by executing:<br><em>chmod 777 tmp/cache<br>chmod 777 tmp/templates_c</em><br>or the equivalent for your platform before continuing.</p>';
    echo '</body></html>';
    return FALSE;
  }

  return TRUE;
}
?>

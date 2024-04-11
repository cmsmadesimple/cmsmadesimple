<?php
/**
 * Class for phar resource
 * currently only used for debugging purposes
 * @package cms_autoinstaller
 */

namespace cms_autoinstaller;

use __appbase\app;
use __appbase\cms_smarty;
use Phar;
use PharException;
use Smarty_Resource_Custom;
use function __appbase\get_app;

class smarty_phar_resource  extends Smarty_Resource_Custom
{
  
  /**
   * Fetch a template and its modification time from database
   *
   * @param string  $name   template name
   * @param string  $source template source
   * @param integer $mtime  template modification timestamp (epoch)
   *
   * @return void
   * @throws \Exception
   */
  protected function fetch($name, &$source, &$mtime)
  {
    $OS = \PHP_OS;
    
    // Path to the Phar archive
    $pharPath = Phar::running();
    
    
    if('WINNT' === $OS || 'WIN32' === $OS)
    {
      # we are on windows
      // Create a temporary directory for extraction
      $tempDir = \sys_get_temp_dir() . '/phar_extraction_' . \uniqid('', FALSE);
      
      if(!\mkdir($tempDir) && !\is_dir($tempDir))
      {
        throw new \RuntimeException(\sprintf('Directory "%s" was not created', $tempDir));
      }
      
      $phar = new Phar($pharPath);
      
      try
      {
        // Extract files from the Phar archive to the temporary directory
        $phar->extractTo($tempDir, 'app/templates/'. $name, TRUE);
        #$phar->extractTo($tempDir);
      }
      catch (PharException $e)
      {
        // Handle any exceptions that occur during extraction
        echo 'Error extracting files: ' . $e->getMessage();
      }
      
      // Check if the extracted files exist
      $tplFile = $tempDir . '/app/templates/' . $name;
      
      if(\file_exists($tplFile))
      {
        $files[] = $tplFile;
      }
      else
      {
        // The file could not be extracted
        echo "Failed to extract template file: $tplFile";
      }
      
      foreach( $files as $one )
      {
        if( \file_exists($one) )
        {
          $source = @\file_get_contents($one);
          $mtime = @\filemtime($one);
          return;
        }
      }
    }
    else
    {
      # we are on linux
      $source = cms_smarty::get_instance()->fetch($name);
    }
    
    // Clean up: Remove the temporary directory
//    if (\is_dir($tempDir)) {
//      // Recursively remove the temporary directory and its contents
//      $files = new \RecursiveIteratorIterator(
//        new \RecursiveDirectoryIterator($tempDir, \RecursiveDirectoryIterator::SKIP_DOTS),
//        \RecursiveIteratorIterator::CHILD_FIRST
//      );
//      foreach ($files as $file) {
//        if ($file->isDir()) {
//          \rmdir($file->getRealPath());
//        } else {
//          \unlink($file->getRealPath());
//        }
//      }
//      \rmdir($tempDir);
//    }
  
  }
}

?>
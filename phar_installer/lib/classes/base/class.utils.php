<?php

namespace __appbase;

class utils
{
    private static $_writable_error = [];

    private function __construct() {}
    
    public static function redirect($to)
    {
        $_SERVER['PHP_SELF'] = null;
        $schema = '443' == $_SERVER['SERVER_PORT'] ? 'https' : 'http';
        $host = \strlen($_SERVER['HTTP_HOST'])? $_SERVER['HTTP_HOST']: $_SERVER['SERVER_NAME'];
        
        $components = \parse_url($to);

        if (\count($components) > 0) {
            $to =  (isset($components['scheme']) && startswith($components['scheme'], 'http') ? $components['scheme'] : $schema) . '://';
            $to .= $components['host'] ?? $host;
            $to .= isset($components['port']) ? ':' . $components['port'] : '';
            if(isset($components['path'])) {
                if(\in_array($components['path'][0], ['\\', '/'])) { //Path is absolute, just append.
                    $to .= $components['path'];
                }
                //Path is relative, append current directory first.
                else if (isset($_SERVER['PHP_SELF'])) { //Apache
                    $to .= (\strlen(\dirname($_SERVER['PHP_SELF'])) > 1 ? \dirname($_SERVER['PHP_SELF']) . '/' : '/') . $components['path'];
                }
                else if (isset($_SERVER['REQUEST_URI'])) { //Lighttpd
                    if (endswith($_SERVER['REQUEST_URI'], '/'))
                        $to .= (\strlen($_SERVER['REQUEST_URI']) > 1 ? $_SERVER['REQUEST_URI'] : '/') . $components['path'];
                    else
                        $to .= (\strlen(\dirname($_SERVER['REQUEST_URI'])) > 1 ? \dirname($_SERVER['REQUEST_URI']) . '/' : '/') . $components['path'];
                }
            }
            else {
                $to .= $_SERVER['REQUEST_URI'];
            }
            $to .= isset($components['query']) ? '?' . $components['query'] : '';
            $to .= isset($components['fragment']) ? '#' . $components['fragment'] : '';
        }
        else {
            $to = $schema."://".$host."/".$to;
        }

        \session_write_close();

        if(\headers_sent() ) {

            // use javascript instead
            echo '<script type="text/javascript"><!-- location.replace("'.$to.'"); // --></script><noscript><meta http-equiv="Refresh" content="0;URL='.$to.'"></noscript>';
            exit;
        }
        else {
            \header("Location: $to");
            exit();
        }
    }

    public static function to_bool($in,$strict = FALSE)
    {
        $in = \strtolower((string) $in);
        if( \in_array($in, ['1', 'y', 'yes', 'true', 't', 'on']) ) return TRUE;
        if( \in_array($in, ['0', 'n', 'no', 'false', 'f', 'off']) ) return FALSE;
        if( $strict ) return null;
        return (bool)$in;
    }

    public static function clean_string($val)
    {
        if( !$val ) return $val;
        $val = (string) $val;
        $val = \preg_replace("/\\\$/", '$', $val);
        $val = \preg_replace("/\r/", "", $val);
        $val = \str_replace(['!', "'"], ['!', "'"], $val);
        
        return \strip_tags($val);
    }
  
  /**
   * cleans passwords for config.php mainly db pass.
   * we don't want quotes on the string
   * @since 1.3.13
   * @param $val
   *
   * @return string|string[]
   */
    public static function clean_password($val)
    {
      if( !$val ) return $val;
      $val = \trim((string) $val );
      $val = \str_replace(["'", '"'], '', $val);
  
      return $val;
    }

    public static function get_sys_tmpdir()
    {
        $vars = ['TMP', 'TMPDIR', 'TEMP'];
        foreach( $vars as $var ) {
            if( isset($_ENV[$var]) && $_ENV[$var] ) {
                $tmp = \realpath($_ENV[$var]);
                if( $tmp && @\is_dir($tmp) && @\is_writable($tmp) ) return $tmp;
            }
        }

        $tmpdir = \ini_get('upload_tmp_dir');
        if( $tmpdir && @\is_dir($tmpdir) && @\is_writable($tmpdir) ) return $tmpdir;
        
        
        /**
         * sys_get_temp_dir() is available in PHP >= 5.3, so we can use it.
         * In any case, this should wrap it, no more need for a fallback.
         */
//        if( \function_exists('sys_get_temp_dir') ) {
            $tmp = \rtrim(\sys_get_temp_dir(), '\\/');
            if( $tmp && @\is_dir($tmp) && @\is_writable($tmp) ) return $tmp;
//        }

        /** safe mode was removed in PHP 5.4.0. */
//        if('1' != \ini_get('safe_mode')) {
            // last ditch effort to find a place to write to.
            $tmp = @\tempnam('', 'xxx');
            if($tmp && \file_exists($tmp) ) {
                @\unlink($tmp);
                return \realpath(\dirname($tmp));
            }
//        }
        
        throw new \RuntimeException('Could not find a writable location for temporary files');
    }

    public static function is_email($str)
    {
        return \filter_var($str, \FILTER_VALIDATE_EMAIL);
    }

    /**
     * Check the permissions of a directory recursively to make sure that
     * we have write permission to all files and folders.
     *
     * @param  string  $path Start directory.
     * @param  bool    $ignore_specialfiles  Optionally ignore special system files in the check.  Special files include files beginning with ., and php.ini files.
     * @return bool
     */
    public static function is_directory_writable( $path, $ignore_specialfiles = TRUE )
    {
        if ('/' != \substr ($path , \strlen ($path ) - 1 )) $path .= '/' ;
        
        $p      = '';
        $result = TRUE;
        
        if( $handle = @\opendir($path ) ) {
            while( false !== ( $file = \readdir($handle ) ) ) {
                if( $file == '.' || $file == '..' ) continue;

                // ignore dotfiles, except .htaccess.
                if( $ignore_specialfiles ) {
                    if( $file[0] == '.' && $file != '.htaccess' ) continue;
                    if( $file == 'php.ini' ) continue;
                }

                $p = $path.$file;
                if( !@\is_writable($p ) ) {
                    self::$_writable_error[] = $p;
                    @\closedir($handle );
                    return FALSE;
                }

                if( @\is_dir($p ) ) {
                    $result = self::is_directory_writable( $p, $ignore_specialfiles );
                    if( !$result ) {
                        self::$_writable_error[] = $p;
                        @\closedir($handle );
                        return FALSE;
                    }
                }
            }
            @\closedir($handle );
        }
        else {
            self::$_writable_error[] = $p;
            return FALSE;
        }

        return TRUE;
    }


    public static function get_writable_error()
    {
        return self::$_writable_error;
    }

    public static function rrmdir($dir)
    {
        if (\is_dir($dir)) {
            $objects = \scandir($dir);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (\filetype($dir . '/' . $object) == 'dir') self::rrmdir($dir . '/' . $object); else \unlink($dir .
                                                                                                                   '/' . $object);
                }
            }
            \reset($objects);
            \rmdir($dir);
        }
    }

} // end of class
?>

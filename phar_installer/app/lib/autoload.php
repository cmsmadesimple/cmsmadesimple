<?php
function cms_install_autoload( $in_classname )
{
    $namespace = $classname = null;
    $pos = strrpos( $in_classname, '\\' );
    if( $pos !== FALSE ) {
        $namespace = substr( $in_classname, 0, $pos );
        $classname = substr( $in_classname, $pos + 1 );
    }

    $map = null;
    $map['cms_autoinstaller'] = [ __DIR__, dirname(__DIR__) ];
    $top = dirname(dirname(__DIR__));
    $tmp1 = $top.'/lib/classes/base';
    $tmp2 = $top.'/lib/classes';
    $map['__appbase'] = [ $tmp1, $tmp2 ];

    foreach( $map as $one_namespace => $one_list ) {
        if( $namespace == $one_namespace ) {
            if( !is_array( $one_list ) ) $one_list = [ $one_list ];
            foreach( $one_list as $one_path ) {
                $fn = $one_path."/class.$classname.php";
                if( is_file( $fn ) ) {
                    require_once( $fn );
                    return;
                }
            }
        }
    }

    $class_n = str_replace('\\','/',$classname);
    $dn = $top.'/lib/'.dirname( $class_n );
    $bn = basename( $class_n );
    $patterns = [ 'class.%s.php', 'interface.%s.php', 'abstract.%s.php', 'trait.%s.php' ];
    foreach( $patterns as $one ) {
        $fn = $dn.'/'.sprintf( $one, $bn );
        if( is_file( $fn ) ) {
            require_once( $fn );
            return;
        }
    }
}

spl_autoload_register( 'cms_install_autoload' );
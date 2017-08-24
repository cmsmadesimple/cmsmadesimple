<?php
namespace ModuleManager;
use \CMSMS\CLI\App;
use \CMSMS\CLI\GetOptExt\Command;
use \CMSMS\CLI\GetOptExt\Option;
use \CMSMS\CLI\GetOptExt\GetOpt;
use \GetOpt\Operand;

class ListModulesCommand extends Command
{
    public function __construct( App $app )
    {
        parent::__construct( $app, 'moma-list' );
        $this->setDescription('Display verbose information about installed and available modules');
        $this->addOption( Option::Create('v','verbose')->SetDescription('Display information verbosely') );
        $this->addOption( Option::Create('m','module', GetOpt::REQUIRED_ARGUMENT )->SetDescription('Display information about a specific module (in verbose mode)') );
    }

    protected function do_verbose_module( \ModuleManagerModuleInfo $info )
    {
        $do_line = function( $prompt, $value ) {
            printf("%-20s: %s\n", $prompt, $value );
        };

        $do_bool = function( $prompt, $value ) {
            $out = ($value) ? 'Yes' : 'No';
            printf("%-20s: %s\n", $prompt, $out );
        };

        $do_line('MODULE',$info['name'] );
        $do_line('DESCRIPTION',$info['description'] );
        $do_line('INSTALLED VERSION',$info['installed_version'] );
        $do_line('FILE VERSION',$info['version'] );
        $do_line('DIR',$info['dir'] );
        $do_bool('ROOT_WRITABLE', $info['root_writable'] );
        $do_bool('WRITABLE', $info['writable'] );
        $do_line('AUTHOR',$info['author'] );
        $do_line('AUTHOREMAIL',$info['authoremail'] );
        $do_bool('LAZYLOADADMIN',$info['lazyloadadmin'] );
        $do_bool('LAZYLOADFRONTEND',$info['lazyloadfrontend'] );
        $do_line('MINCMSVERSION',$info['mincmsversion']);
        $do_bool('HAVE_CUSTOMIZATIONS', $info['has_custom'] );
        $do_bool('AVAILABLE', !$info['notavailable'] );
        $do_bool('SYSTEM_MODULE', $info['is_system_module'] );
        $do_bool('HAVE_METADATA', $info['has_meta'] );
        $do_bool('NEEDS_UPGRADE', $info['needs_upgrade'] );
        $do_bool('DEPRECATED', $info['deprecated'] );
        if( $info['depends'] ) {
            foreach( $info['depends'] as $name => $ver ) {
                $do_line('DEPENDENCY',$name.' - '.$ver);
            }
        }
    }

    protected function do_verbose_report( array $allmoduleinfo )
    {
        foreach( $allmoduleinfo as $info ) {
            $this->do_verbose_module( $info );
            echo "\n";
        }
    }

    protected function do_normal_report( array $allmoduleinfo )
    {
        $column_widths = [ 'name'=>null, 'version'=>null, 'status'=>null, 'system_module'=>null ];

        $get_statuses = function( \ModuleManagerModuleInfo $info ) {
            $out = [];
            if( $info['has_custom'] ) $out[] = 'Has odule custom!';
            // if( !$info['has_meta'] ) $out[] = 'No meta file!';
            if( !$info['root_writable'] ) $out[] = 'Not writable';
            if( $info['notavailable'] ) $out[] = 'Not available';
            if( $info['missingdeps'] ) $out[] = 'Missing dependencies';
            if( $info['needs_upgrade'] ) $out[] = 'Needs upgrade';
            if( $info['e_status'] == 'db_newer' ) $out[] = 'Database version ('.$info['installed_version'].') is newer';

            sort($out);
            return $out;
        };

        // build the data columns
        $rows = [];
        $rows[] = [ 'name'=>'Name', 'version'=>'Version', 'status'=>'Status', 'system_module'=>'System' ];
        foreach( $allmoduleinfo as $one ) {
            $row = ['name'=>$one['name'], 'version'=>$one['version'], 'system_module'=>$one['system_module'] ];
            $row['status'] = $get_statuses( $one );
            $rows[] = $row;
        }

        // now get column widths.
        foreach( $rows as $row ) {
            foreach( $row as $key => $val ) {
                if( $key == 'status' ) {
                    $len = $column_widths[$key];
                    if( is_array($val) ) {
                        foreach( $val as $one ) {
                            $len = max($len,strlen($one));
                        }
                    } else {
                        $len = max($len,strlen($val));
                    }
                    $column_widths[$key] = $len;
                } else {
                    if( array_key_exists($key,$column_widths) ) {
                        $column_widths[$key] = max($column_widths[$key],strlen($val));
                    }
                }
            }
        }

        $do_line = function( $name, $version, $status, $system_module ) use ( $column_widths ) {
            // pad to N chars with text on left.
            $pad_left = function( $str, $width ) {
                echo str_pad( $str, (int) $width, ' ', STR_PAD_RIGHT ).'  ';
            };

            // name column
            $pad_left( $name, $column_widths['name'] );
            // version column
            $pad_left( $version, $column_widths['version'] );
            // status column
            $pad_left( $status, $column_widths['status'] );
            // system module
            $pad_left( $system_module, $column_widths['system_module'] );
            echo PHP_EOL;
        };

        // now we do the output
        $row = array_shift( $rows );
        $do_line( $row['name'], $row['version'], $row['status'], $row['system_module'] );
        foreach( $rows as $row ) {
            $first = true;
            if( is_array($row['status']) ) {
                if( !count($row['status']) ) {
                    $do_line( $row['name'], $row['version'], null, ($row['system_module']) ? 'Yes' : '' );
                    continue;
                }

                foreach( $row['status'] as $status ) {
                    if( $first ) {
                        $do_line( $row['name'], $row['version'], $status, ($row['system_module']) ? 'Yes' : '' );
                    } else {
                        $do_line( null, null, $status, null );
                    }
                    $first = false;
                }
            }
            else {
                $do_line( $row['name'], $row['version'], $row['status'], ($row['system_module']) ? 'Yes' : '' );
            }
        }
    }

    public function handle()
    {
        $allmoduleinfo = \ModuleManagerModuleInfo::get_all_module_info(TRUE);
        $verbose = $this->getOption('verbose')->value();
        $module = $this->getOption('module')->value();
        if( $module ) {
            foreach( $allmoduleinfo as $one ) {
                if( $one['name'] == $module ) {
                    $this->do_verbose_module( $one );
                    return;
                }
            }
            throw new \RuntimeException('Could not find module information for '.$module);
        }
        if( $verbose ) {
            $this->do_verbose_report( $allmoduleinfo );
            return;
        }
        $this->do_normal_report( $allmoduleinfo );
    }
} // end of class.
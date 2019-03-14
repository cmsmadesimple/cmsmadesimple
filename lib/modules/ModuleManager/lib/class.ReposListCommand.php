<?php
namespace ModuleManager;
use \CMSMS\CLI\App;
use \CMSMS\CLI\GetOptExt\Command;
use \CMSMS\CLI\GetOptExt\Option;
use \CMSMS\CLI\GetOptExt\GetOpt;
use \GetOpt\Operand;

class ReposListCommand extends Command
{
    public function __construct( App $app )
    {
        parent::__construct( $app, 'moma-repos-list' );
        $this->addOperand( new Operand( 'module', Operand::REQUIRED ) );
        $this->addOption( Option::Create('l','latest')->setDescription('Return only the latest version') );
        $this->addOption( Option::Create('v','verbose')->setDescription('Return only the latest version') );
    }

    public function getShortDescription()
    {
        return 'List available versions of a module;';
    }

    public function getLongDescription()
    {
        return 'This command will return a list of all versions of a specific module that are compatible with your CMSMS installation.';
    }

    protected function list_table( array $list )
    {
        $hdr = [ 'name'=>'Name', 'version'=>'Version', 'filename'=>'Filename', 'size'=>'Size', 'date'=>'Date' ];
        $out = [ $hdr ];
        foreach( $list as $row ) {
            $out[] = $row;
        }

        $lengths = [ 'name'=>0,'version'=>0,'filename'=>0,'size'=>0,'date'=>0 ];
        foreach( $out as $row ) {
            foreach( $lengths as $key => $val ) {
                $lengths[$key] = max($val,strlen($row[$key]));
            }
        }

        $fmt = "%-{$lengths['name']}s  %-{$lengths['version']}s  %-{$lengths['filename']}s  %-{$lengths['date']}s  %{$lengths['size']}s\n";
        echo "$fmt\n";
        foreach( $out as $row ) {
            printf( $fmt, $row['name'], $row['version'], $row['filename'], $row['date'], $row['size']);
        }
    }

    protected function list_verbose( $list )
    {
        $do_row = function( array $row ) {
            $out = null;
            $fmt = "%-8s: %s\n";
            $out .= sprintf($fmt,'NAME',$row['name']);
            $out .= sprintf($fmt,'VERSION',$row['version']);
            $out .= sprintf($fmt,'FILENAME',$row['filename']);
            $out .= sprintf($fmt,'DATE',$row['date']);
            $out .= sprintf($fmt,'SIZE',$row['size']);
            $out .= sprintf($fmt,'MD5',$row['md5sum']);
            return $out;
        };

        $out = null;
        foreach( $list as $row ) {
            $out .= $do_row( $row );
            $out .= "\n";
        }
        return $out;
    }

    public function handle()
    {
        try {
            $module = $this->getOperand('module')->value();
            $latest = ($this->getOption('latest')->value()) ? true : false;
            $verbose = $this->getOption('verbose')->value();
            $list = \modulerep_client::get_repository_modules( $module, $latest, TRUE );

            if( !$list || count($list) != 2 || $list[0] != 1 ) throw new \RuntimeException('No matches');
            $list = $list[1];
            $list = array_slice($list,0,50);

            if( $verbose ) {
                // returns
                return $this->list_verbose( $list );
            }
            else {
                // echoes
                $this->list_table( $list );
            }
        }
        catch( \ModuleNoDataException $e ) {
            throw new \RuntimeException("Module $module not found in repository");
        }

    }
} // end of class.

<?php
namespace ModuleManager;
use \CMSMS\CLI\App;
use \CMSMS\CLI\GetOptExt\Command;
use \CMSMS\CLI\GetOptExt\Option;
use \CMSMS\CLI\GetOptExt\GetOpt;
use \GetOpt\Operand;

class ReposGetXMLCommand extends Command
{
    public function __construct( App $app )
    {
        parent::__construct( $app, 'moma-repos-getxml' );
        $this->addOperand( new Operand( 'module', Operand::REQUIRED ) );
        $this->addOption( Option::create(null,'xmlver', GetOpt::REQUIRED_ARGUMENT )->setDescription('Download a specific version') );
    }

    protected function getShortDescription()
    {
        return 'Download a module XML file from the repository';
    }

    protected function getLongDescription()
    {
        return <<<EOT
This command will download a single module XML file to the local machine for later import.  No dependency checks are performed.
By default this command will download the last released version of the specified module.
EOT;
    }

    public function handle()
    {
        try {
            $moma = \cms_utils::get_module('ModuleManager');
            $module = $this->getOperand('module')->value();

            $data = \modulerep_client::get_modulelatest( [ $module ] );
            if( !$data ) throw new \RuntimeException("Module $module not found in repository");
            if( count($data) > 1 ) throw new \RuntimeException("Internal error: multiple results returned");
            $data = $data[0];

            $filename = $data['filename'];
            $md5sum = $data['md5sum'];
            $tmpfile = \modulerep_client::get_repository_xml( $filename );
            if( !$tmpfile ) throw new \RuntimeException("Problem downloading ".$filename." no data returned");
            $newsum = md5_file( $tmpfile );
            if( $md5sum != $newsum ) throw new \RuntimeException("Problem downloading $filename, checksum fail");

            copy( $tmpfile, $filename );
            unlink( $tmpfile );
            echo $filename."\n";
        }
        catch( \ModuleNoDataException $e ) {
            throw new \RuntimeException("Module $module not found in repository");
        }

    }
} // end of class.

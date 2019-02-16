<?php
namespace ModuleManager;
use \CMSMS\CLI\App;
use \CMSMS\CLI\GetOptExt\Command;
use \CMSMS\CLI\GetOptExt\Option;
use \CMSMS\CLI\GetOptExt\GetOpt;
use \GetOpt\Operand;

class ReposDependsCommand extends Command
{
    public function __construct( App $app )
    {
        parent::__construct( $app, 'moma-repos-xmldepends' );
        $this->addOperand( new Operand( 'filename', Operand::REQUIRED ) );
    }

    protected function getShortDescription()
    {
        return 'List the dependencies of a specific repository XML file';
    }

    protected function getLongDescription()
    {
        return 'This command will return a list of all dependencies of a specific repository XML file';
    }

    public function handle()
    {
        try {
            $filename = $this->getOperand('filename')->value();

            $data = \modulerep_client::get_module_depends( $filename );
            if( !$data || !is_array($data) || !$data[0] ) throw new \RuntimeException('File not found');

            $data = $data[1];
            foreach( $data as $dep ) {
                printf("%s (%s)\n",$dep['name'],$dep['version']);
            }
        }
        catch( \ModuleNoDataException $e ) {
            throw new \RuntimeException("Module $module not found in repository");
        }
    }
} // end of class.

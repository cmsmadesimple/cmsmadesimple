<?php
namespace ModuleManager;
use \CMSMS\CLI\App;
use \CMSMS\CLI\GetOptExt\Command;
use \CMSMS\CLI\GetOptExt\Option;
use \CMSMS\CLI\GetOptExt\GetOpt;
use \GetOpt\Operand;

class PingModuleServerCommand extends Command
{
    public function __construct( App $app )
    {
        parent::__construct( $app, 'moma-ping' );
        $this->setDescription('Ping the module server, and test for connectivity');
    }

    public function handle()
    {
        $connection_ok = utils::is_connection_ok();
        if( !$connection_ok ) throw new \RuntimeException('A problem occurred communicating with the module server');
    }
} // end of class.

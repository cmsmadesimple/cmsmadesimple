<?php
namespace ModuleManager;
use \CMSMS\CLI\App;
use \CMSMS\CLI\GetOptExt\Command;
use \CMSMS\CLI\GetOptExt\Option;
use \CMSMS\CLI\GetOptExt\GetOpt;
use \GetOpt\Operand;

class ModuleRemoveCommand extends Command
{
    public function __construct( App $app )
    {
        parent::__construct( $app, 'moma-remove' );
        $this->setDescription('Remove an uninstalled module from the filesystem');
        $this->addOperand( new Operand( 'module', Operand::REQUIRED ) );
    }

    public function handle()
    {
        $ops = \ModuleOperations::get_instance();
        $moma = \cms_utils::get_module('ModuleManager');
        $module = $this->getOperand('module')->value();
        $info = new \ModuleManagerModuleInfo( $module );

        if( !$info['dir'] ) throw new \RuntimeException("Nothing is known about module ".$module);
        if( $info['installed'] ) throw new \RuntimeException("Cannot remove module ".$module.' because it is installed');

        $result = recursive_delete( $info['dir'] );
        if( !$result ) throw new \RuntimeException("Error removing module ".$module);

        audit('',$moma->GetName(),'Removed module '.$module);
        echo "Removed module $module\n";
    }
} // end of class.

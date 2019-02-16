<?php
namespace ModuleManager;
use \CMSMS\CLI\App;
use \CMSMS\CLI\GetOptExt\Command;
use \CMSMS\CLI\GetOptExt\Option;
use \CMSMS\CLI\GetOptExt\GetOpt;
use \GetOpt\Operand;

class ModuleImportCommand extends Command
{
    public function __construct( App $app )
    {
        parent::__construct( $app, 'moma-import' );
        $this->setDescription('Import a module XML file into CMSMS');
        $this->addOperand( new Operand( 'filename', Operand::REQUIRED ) );
    }

    public function handle()
    {
        $moma = \cms_utils::get_module('ModuleManager');
        $ops = \ModuleOperations::get_instance();
        $filename = $this->getOperand('filename')->value();
        if( !is_file( $filename) ) throw new \RuntimeException("Could not find $filename to import");

        \CMSMS\HookManager::do_hook('ModuleManager::BeforeModuleImport', [ 'file'=>$filename ] );
        $moma->get_operations()->expand_xml_package( $filename, true, false );
        \CMSMS\HookManager::do_hook('ModuleManager::AfterModuleImport', [ 'file'=>$filename ] );

        audit('',$moma->GetName(),'Imported Module from '.$filename);
        echo "Imported: $filename\n";
    }
} // end of class.

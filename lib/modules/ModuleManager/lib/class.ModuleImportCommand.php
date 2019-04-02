<?php
namespace ModuleManager;
use ModuleManager;
use CMSMS\hook_manager;
use CMSMS\CLI\App;
use CMSMS\CLI\GetOptExt\Command;
use CMSMS\CLI\GetOptExt\Option;
use CMSMS\CLI\GetOptExt\GetOpt;
use GetOpt\Operand;

class ModuleImportCommand extends Command
{
    private $moma;
    private $hook_mgr;

    public function __construct( App $app, ModuleManager $moma, hook_manager $hm )
    {
        $this->moma = $moma;
        $this->hook_mgr = $hm;
        parent::__construct( $app, 'moma-import' );
        $this->setDescription('Import a module XML file into CMSMS');
        $this->addOperand( new Operand( 'filename', Operand::REQUIRED ) );
    }

    public function handle()
    {
        $moma = $this->moma;
        $ops = \ModuleOperations::get_instance();
        $filename = $this->getOperand('filename')->value();
        if( !is_file( $filename) ) throw new \RuntimeException("Could not find $filename to import");

        $this->hook_mgr->emit('ModuleManager::BeforeModuleImport', [ 'file'=>$filename ] );
        $moma->get_operations()->expand_xml_package( $filename, true, false );
        $this->hook_mgr->emit('ModuleManager::AfterModuleImport', [ 'file'=>$filename ] );

        audit('',$moma->GetName(),'Imported Module from '.$filename);
        echo "Imported: $filename\n";
    }
} // end of class.

<?php
namespace ModuleManager;
use ModuleManager;
use CMSMS\hook_manager;
use CMSMS\CLI\App;
use CMSMS\CLI\GetOptExt\Command;
use CMSMS\CLI\GetOptExt\Option;
use CMSMS\CLI\GetOptExt\GetOpt;
use GetOpt\Operand;

class ModuleExportCommand extends Command
{
    private $moma;
    private $hook_mgr;

    public function __construct( App $app, ModuleManager $moma, hook_manager $hm )
    {
        $this->hook_mgr = $hm;
        $this->moma = $moma;
        parent::__construct( $app, 'moma-export' );
        $this->setDescription('Export a known, installed, and available module to XML format for sharing');
        $this->addOperand( new Operand( 'module', Operand::REQUIRED ) );
    }

    public function handle()
    {
        $ops = \ModuleOperations::get_instance();
        $moma = $this->moma;
        $module = $this->getOperand('module')->value();
        $modinstance = $ops->get_module_instance($module,'',TRUE);
        if( !is_object($modinstance) ) throw new \RuntimeException('Could not instantiate module '.$module);

        $old_display_errors = ini_set('display_errors',0);
        \CmsLangOperations::allow_nonadmin_lang(TRUE);
        \CmsNlsOperations::set_language('en_US');
        $this->hook_mgr->emit('ModuleManager::BeforeModuleExport', [ 'module_name' => $module, 'version' => $modinstance->GetVersion() ] );
        $xmltext = $moma->get_operations()->create_xml_package($modinstance,$message,$files);
        $this->hook_mgr->emit('ModuleManager::AfterModuleExport', [ 'module_name' => $module, 'version' => $modinstance->GetVersion() ] );
        if( $old_display_errors !== FALSE ) ini_set('display_errors',$old_display_errors);

        $xmlname = $modinstance->GetName().'-'.$modinstance->GetVersion().'.xml';
        file_put_contents( $xmlname, $xmltext );

        audit('',$moma->GetName(),'Exported '.$modinstance->GetName().' to '.$xmlname);
        echo "Created: $xmlname\n";
    }
} // end of class.

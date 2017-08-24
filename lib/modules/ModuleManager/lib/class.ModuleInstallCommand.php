<?php
namespace ModuleManager;
use \CMSMS\CLI\App;
use \CMSMS\CLI\GetOptExt\Command;
use \CMSMS\CLI\GetOptExt\Option;
use \CMSMS\CLI\GetOptExt\GetOpt;
use \GetOpt\Operand;

class ModuleInstallCommand extends Command
{
    public function __construct( App $app )
    {
        parent::__construct( $app, 'moma-install' );
        $this->setDescription('Install a module that is known, but not installed');
        $this->addOperand( new Operand( 'module', Operand::REQUIRED ) );
    }

    public function handle()
    {
        $ops = \ModuleOperations::get_instance();
        $moma = \cms_utils::get_module('ModuleManager');
        $module = $this->getOperand('module')->value();

        \CmsLangOperations::allow_nonadmin_lang(TRUE);
        $ops = \ModuleOperations::get_instance();
        $result = $ops->InstallModule($module);
        if( !is_array($result) || !isset($result[0]) ) throw new \RuntimeException("Module installation failed");
        if( $result[0] == FALSE ) throw new \RuntimeException($result[1]);

        $modinstance = $ops->get_module_instance($module,'',TRUE);
        if( !is_object($modinstance) ) throw new \RuntimeException('Problem instantiating module '.$module);

        audit('',$moma->GetName(),'Installed '.$modinstance->GetName().' '.$modinstance->GetVersion());
        echo "Installed: ".$modinstance->GetName().' '.$modinstance->GetVersion()."\n";
    }
} // end of class.
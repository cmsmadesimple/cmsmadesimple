<?php
namespace ModuleManager;
use CMSMS\CLI\App;
use CMSMS\CLI\GetOptExt\Command;
use CMSMS\CLI\GetOptExt\Option;
use CMSMS\CLI\GetOptExt\GetOpt;
use GetOpt\Operand;
use CmsApp;
use CmsLangOperations;
use ModuleManagerModuleInfo;

class ModuleUpgradeCommand extends Command
{
    private $app;

    public function __construct( App $app, CmsApp $gCms )
    {
        $this->app = $gCms;
        parent::__construct( $app, 'moma-upgrade' );
        $this->setDescription('Upgrade a module that is installed, but requires upgrade');
        $this->addOperand( new Operand( 'module', Operand::REQUIRED ) );
    }

    public function handle()
    {
        $ops = $this->app->GetModuleOperations();
        $moma = $ops->get_module_instance('ModuleManager');
        $module = $this->getOperand('module')->value();

        $info = ModuleManagerModuleInfo::get_module_info($module);
        if( !empty($info['missing_deps']) || !$info['can_upgrade'] ) throw new \RuntimeException('Cannot upgrade '.$module.' - dependencies?');
        if( !$info['needs_upgrade'] ) throw new \RuntimeException('module '.$module.' is not in need of upgrade');

        //$modinstance = $ops->get_module_instance($module,'',TRUE);
        //if( !is_object($modinstance) ) throw new \RuntimeException('Problem loading module '.$module);

        CmsLangOperations::allow_nonadmin_lang(TRUE);
        $result = $ops->UpgradeModule($module);
        if( !is_array($result) || !isset($result[0]) ) throw new \RuntimeException("Module upgrade failed");
        if( $result[0] == FALSE ) throw new \RuntimeException($result[1]);

        $modinstance = $ops->get_module_instance($module,'',TRUE);
        if( !is_object($modinstance) ) throw new \RuntimeException('Problem loading module '.$module);

        $this->app->clear_cached_files();

        audit('','cmscli','Upgraded '.$modinstance->GetName().' '.$modinstance->GetVersion());
        echo "Upgraded: ".$modinstance->GetName().' to '.$modinstance->GetVersion()."\n";
    }
} // end of class.

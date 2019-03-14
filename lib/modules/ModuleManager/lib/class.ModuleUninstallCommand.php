<?php
namespace ModuleManager;
use CMSMS\CLI\App;
use CMSMS\CLI\GetOptExt\Command;
use CMSMS\CLI\GetOptExt\Option;
use CMSMS\CLI\GetOptExt\GetOpt;
use GetOpt\Operand;
use CmsApp;

class ModuleUninstallCommand extends Command
{
    private $app;

    public function __construct( App $app, CmsApp $gCms )
    {
        $this->app = $gCms;
        parent::__construct( $app, 'moma-uninstall' );
        $this->setDescription('Uninstall a module... this does not remove module files, but will potentially clear all module data');
        $this->addOperand( new Operand( 'module', Operand::REQUIRED ) );
    }

    public function handle()
    {
        $ops = \ModuleOperations::get_instance();
        $moma = \cms_utils::get_module('ModuleManager');
        $module = $this->getOperand('module')->value();
        $info = new \ModuleManagerModuleInfo( $module );
        if( !$info['dir'] ) throw new \RuntimeException("Nothing is known about module ".$module);
        if( !$info['installed'] ) throw new \RuntimeException("module $module is not installed");

        $instance = $ops->get_module_instance($module);
        if( !is_object( $instance ) ) throw new \RuntimeException('Could not instantiate module '.$module);

        $result = $ops->UninstallModule($module);
        if( $result[0] == FALSE ) throw new \RuntimeException($result[1]);
        $this->app->clear_cached_files();

        echo "Uninstalled module $module\n";
    }
} // end of class.

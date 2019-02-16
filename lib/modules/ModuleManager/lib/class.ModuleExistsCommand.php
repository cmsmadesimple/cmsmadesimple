<?php
namespace ModuleManager;
use \CMSMS\CLI\App;
use \CMSMS\CLI\GetOptExt\Command;
use \CMSMS\CLI\GetOptExt\Option;
use \CMSMS\CLI\GetOptExt\GetOpt;
use \GetOpt\Operand;

class ModuleExistsCommand extends Command
{
    public function __construct( App $app )
    {
        parent::__construct( $app, 'moma-exists' );
        $this->setDescription('Test if a module exists, or is known in any way');
        $this->addOption( Option::Create( 'v','verbose')->setDescription('Enable verbose mode') );
        $this->addOperand( new Operand( 'module', Operand::REQUIRED ) );
    }

    public function handle()
    {
        $verbose = $this->getOption( 'verbose' )->value();
        $module = $this->getOperand( 'module' )->value();

        $allmoduleinfo = \ModuleManagerModuleInfo::get_all_module_info(TRUE);
        foreach( $allmoduleinfo as $one ) {
            if( $one['name'] == $module ) {
                // yep... we know about it.
                if( $verbose ) echo "$module is known\n";
                return;
            }
        }

        if( $verbose ) echo "$module is NOT known\n";
        exit(1);
    }
} // end of class.

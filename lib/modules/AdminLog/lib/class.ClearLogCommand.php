<?php
namespace AdminLog;
use \CMSMS\CLI\App;
use \CMSMS\CLI\GetOptExt\Command;
use \CMSMS\CLI\GetOptExt\Option;
use \CMSMS\CLI\GetOptExt\GetOpt;
use \GetOpt\Operand;

class ClearLogCommand extends Command
{
    public function __construct( App $app )
    {
        parent::__construct( $app, 'adminlog-clear' );
        $this->setDescription('List installed modules');
    }

    public function handle()
    {
        $mod = \cms_utils::get_module('AdminLog');

        $storage = new \AdminLog\storage( $mod );
        $storage->clear();
        audit('','Admin log','Cleared');
    }

}
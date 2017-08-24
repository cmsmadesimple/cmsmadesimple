<?php
namespace Search;
use \CMSMS\CLI\App;
use \CMSMS\CLI\GetOptExt\Command;
use \CMSMS\CLI\GetOptExt\Option;
use \CMSMS\CLI\GetOptExt\GetOpt;
use \GetOpt\Operand;

class ReindexCommand extends Command
{
    public function __construct( App $app )
    {
        parent::__construct( $app, 'search-reindex' );
        $this->setDescription('Re-index all search content');
    }

    public function handle()
    {
        $mod = \cms_utils::get_module('Search');
        $mod->Reindex();
    }
} // end of class.
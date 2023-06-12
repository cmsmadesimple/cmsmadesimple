<?php

namespace cms_autoinstaller;

use __appbase\utils;
use cms_autoinstaller\wizard_step;
use Exception;
use function __appbase\get_app;
use function __appbase\lang;
use function __appbase\smarty;
use function __appbase\translator;

class wizard_step6 extends wizard_step
{
    private $_siteinfo;

    public function run()
    {
        $app = get_app();

        $tz = date_default_timezone_get();
        if( !$tz ) @date_default_timezone_set('UTC');

        $this->_siteinfo = array( 'sitename'=>'','languages'=>[] );
        $wiz = $this->get_wizard();
        $tmp = $wiz->get_data('config');
        if( $tmp ) $this->_siteinfo = array_merge($this->_siteinfo,$tmp);
        $lang = translator()->get_selected_language();
        if( $lang != 'en_US' ) $this->_siteinfo['languages'] = [ $lang ];

        $tmp = $wiz->get_data('siteinfo');
        if( is_array($tmp) && count($tmp) ) $this->_siteinfo = $tmp;
        return parent::run();
    }

    private function validate($siteinfo)
    {
        $action = $this->get_wizard()->get_data('action');
        if( $action !== 'freshen' ) {
            if( !isset($siteinfo['sitename']) || !$siteinfo['sitename'] ) throw new Exception(lang('error_nositename'));
        }
    }

    protected function process()
    {
        $app = get_app();
        $config = $app->get_config();

        if( isset($_POST['sitename']) ) $this->_siteinfo['sitename'] = trim(utils::clean_string($_POST['sitename']));
        if( isset($_POST['languages']) && is_array($_POST['languages']) ) {
            $tmp = array();
            foreach ( $_POST['languages'] as $lang ) {
                $tmp[] = utils::clean_string($lang);
            }
            $this->_siteinfo['languages'] = $tmp;
        }

        $wiz = $this->get_wizard();
        $wiz->set_data('siteinfo',$this->_siteinfo);
        try {
            $this->validate($this->_siteinfo);
            $url = $wiz->next_url();
            if( $config['nofiles'] ) $url = $wiz->step_url(8);
            utils::redirect($url);
        }
        catch( Exception $e ) {
            $smarty = smarty();
            $smarty->assign('error',$e->GetMessage());
        }
    }

    protected function display()
    {
        parent::display();
        $wiz = $this->get_wizard();
        $action = $wiz->get_data('action');
        $languages = get_app()->get_language_list();
        unset($languages['en_US']);

        $smarty = smarty();
        $smarty->assign('action',$action)
          ->assign('verbose',$wiz->get_data('verbose',0))
          ->assign('siteinfo',$this->_siteinfo)
          ->assign('yesno',array('0'=>lang('no'),'1'=>lang('yes')))
          ->assign('language_list',$languages)
          ->display('wizard_step6.tpl');
        $this->finish();
    }
} // end of class

?>

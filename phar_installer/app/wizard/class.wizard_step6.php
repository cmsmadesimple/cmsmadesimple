<?php

namespace cms_autoinstaller;
use \__appbase;

class wizard_step6 extends \cms_autoinstaller\wizard_step
{
    private $_siteinfo;

    public function run()
    {
        $app = \__appbase\get_app();

        $tz = \date_default_timezone_get();
        if( !$tz ) @\date_default_timezone_set('UTC');

        $this->_siteinfo = ['sitename' =>'', 'languages' =>[]];
        $tmp = $this->get_wizard()->get_data('config');
        if( $tmp ) $this->_siteinfo = \array_merge($this->_siteinfo, $tmp);
        $lang = \__appbase\translator()->get_selected_language();
        if('en_US' != $lang) $this->_siteinfo['languages'] = [$lang ];

        $tmp = $this->get_wizard()->get_data('siteinfo');
        if(\is_array($tmp) && \count($tmp) ) $this->_siteinfo = $tmp;
        return parent::run();
    }

    private function validate($siteinfo)
    {
        $action = $this->get_wizard()->get_data('action');
        if('freshen' !== $action) {
            if( !isset($siteinfo['sitename']) || !$siteinfo['sitename'] ) throw new \Exception(\__appbase\lang('error_nositename'));
        }
    }

    protected function process()
    {
        $app = \__appbase\get_app();
        $config = $app->get_config();

        if( isset($_POST['sitename']) ) $this->_siteinfo['sitename'] = \trim(\__appbase\utils::clean_string($_POST['sitename']));
        if( isset($_POST['languages']) ) {
            $tmp = [];
            foreach ( $_POST['languages'] as $lang ) {
                $tmp[] = \__appbase\utils::clean_string($lang);
            }
            $this->_siteinfo['languages'] = $tmp;
        }

        $this->get_wizard()->set_data('siteinfo',$this->_siteinfo);
        try {
            $this->validate($this->_siteinfo);
            $url = $this->get_wizard()->next_url();
            if( $config['nofiles'] ) $url = $this->get_wizard()->step_url(8);
            \__appbase\utils::redirect($url);
        }
        catch( \Exception $e ) {
            $smarty = \__appbase\smarty();
            $smarty->assign('error',$e->GetMessage());
        }
    }
    
    /**
     * @throws \SmartyException
     */
    protected function display()
    {
        parent::display();
        $action = $this->get_wizard()->get_data('action');

        $smarty = \__appbase\smarty();
        $smarty->assign('action',$action);
        $smarty->assign('verbose',$this->get_wizard()->get_data('verbose',0));
        $smarty->assign('siteinfo',$this->_siteinfo);
        $smarty->assign('yesno', ['0' =>\__appbase\lang('no'), '1' =>\__appbase\lang('yes')]);
        $languages = \__appbase\get_app()->get_language_list();
        unset($languages['en_US']);
        $smarty->assign('language_list',$languages);

        $smarty->display('wizard_step6.tpl');
        $this->finish();
    }
} // end of class

?>

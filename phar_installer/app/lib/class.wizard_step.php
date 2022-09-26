<?php

namespace cms_autoinstaller;

use __appbase\app;
use __appbase\wizard;
use __appbase\wizard_step;
use Exception;
use function __appbase\get_app;
use function __appbase\lang;
use function __appbase\smarty;

abstract class wizard_step extends wizard_step
{
  static $_registered;

  public function __construct()
  {
    $dd = get_app()->get_destdir();
    if( !$dd ) throw new Exception('Session Failure');

    if( !self::$_registered ) {
      smarty()->registerPlugin('function','wizard_form_start', array($this,'fn_wizard_form_start'));
      smarty()->registerPlugin('function','wizard_form_end', array($this,'fn_wizard_form_end'));
      smarty()->addPluginsDir(app::get_rootdir().'/lib/plugins');
      self::$_registered = 1;
    }

    smarty()->assign('version',get_app()->get_dest_version());
    smarty()->assign('version_name',get_app()->get_dest_name());
    smarty()->assign('dir',get_app()->get_destdir());
    smarty()->assign('in_phar',get_app()->in_phar());
    smarty()->assign('cur_step',$this->cur_step());
  }

  public function fn_wizard_form_start($params, $smarty)
  {
      echo '<form method="POST" action="'.$_SERVER['REQUEST_URI'].'">';
  }

  public function fn_wizard_form_end($params, $smarty)
  {
      echo '</form>';
  }

  protected function get_primary_title()
  {
      $app = get_app();
      $action = $this->get_wizard()->get_data('action');
      $str = null;
      switch( $action ) {
      case 'upgrade':
          $str = lang('action_upgrade',$app->get_dest_version());
          break;
      case 'freshen':
          $str = lang('action_freshen',$app->get_dest_version());
          break;
      case 'install':
      default:
          $str = lang('action_install',$app->get_dest_version());
      }
      return $str;
  }

  protected function display()
  {
      $app = get_app();
      smarty()->assign('wizard_steps',$this->get_wizard()->get_nav());
      smarty()->assign('title',$this->get_primary_title());
  }

  public function error($msg)
  {
      $msg = addslashes($msg);
      echo '<script type="text/javascript">add_error(\''.$msg.'\');</script>'."\n";
      flush();
  }

  public static function verbose($msg)
  {
      $msg = addslashes($msg);
      $verbose = wizard::get_instance()->get_data('verbose');
      if( $verbose )  echo '<script type="text/javascript">add_verbose(\''.$msg.'\');</script>'."\n";
      flush();
  }

  public function message($msg)
  {
      $msg = addslashes($msg);
      echo '<script type="text/javascript">add_message(\''.$msg.'\');</script>'."\n";
      flush();
  }

  public function set_block_html($id,$html)
  {
      $html = addslashes($html);
      echo '<script type="text/javascript">set_block_html(\''.$id.'\',\''.$html.'\');</script>'."\n";
      flush();
  }

  protected function finish()
  {
      echo '<script type="text/javascript">finish();</script>'."\n";
      flush();
  }

}

?>

<?php
namespace cms_autoinstaller\cli_step;
use cms_autoinstaller\cli_task;
use __appbase\console;
use __appbase\translator;

class step_5 extends cli_task
{
    protected function ask_questions( $console )
    {
        $param_get = function( $arr, $key, $dflt = null ) {
            if( is_array( $arr ) && isset( $arr[$key]) ) return $arr[$key];
            return $dflt;
        };

        $options = $this->app()->get_options();
        while( 1 ) {
            $console->lf();

            $dflt = $param_get( $options, 'username' );
            $options['username'] = $console->ask_required_string_cb(
                function( $val, $console ) {
                    if( ! $val ) {
                        $console->show("> Admin username: ");
                    } else {
                        $console->show("> Admin username [$val]: ");
                    }
                }, $dflt );

            $dflt = $param_get( $options, 'password' );
            $options['password'] = $console->ask_required_string_cb(
                function( $val, $console ) {
                    if( ! $val ) {
                        $console->show("> Admin password: ");
                    } else {
                        $console->show("> Admin password [$val]: ");
                    }
                }, $dflt );

            $dflt = $param_get( $options, 'repeatpw' );
            $options['repeatpw'] = $console->ask_string_cb(
                function( $val, $console ) {
                    if( ! $val ) {
                        $console->show("> Admin password again: ");
                    } else {
                        $console->show("> Admin password again [$val]: ");
                    }
                } );

            $dflt = $param_get( $options, 'emailaddr' );
            $options['emailaddr'] = $console->ask_string_cb(
                function( $val, $console ) {
                    if( ! $val ) {
                         $console->show("> Admin email address: ");
                    } else {
                        $console->show("> Admin email address [$val]: ");
                    }
                }, $dflt );

            try {
                $this->validate( $options );
                break;
            }
            catch( \Exception $e ) {
                $console->lf()->show( 'ERROR: '.$e->GetMessage(), 'red_bg' )->lf();
            }
        }
    }

    protected function validate($acct)
    {
        if( !isset($acct['username']) || $acct['username'] == '' ) throw new \Exception(\__appbase\lang('error_adminacct_username'));
        if( !isset($acct['password']) || $acct['password'] == '' || strlen($acct['password']) < 6 ) {
            throw new \Exception(\__appbase\lang('error_adminacct_password'));
        }
        if( !isset($acct['repeatpw']) || $acct['repeatpw'] != $acct['password'] ) {
            throw new \Exception(\__appbase\lang('error_adminacct_repeatpw'));
        }
        if( isset($acct['emailaddr']) && $acct['emailaddr'] != '' && !\__appbase\utils::is_email($acct['emailaddr']) ) {
            throw new \Exception(\__appbase\lang('error_adminacct_emailaddr'));
        }
    }

    public function run()
    {
        // ask admin login info
        $op = $this->app()->get_op();
        if( $this->app()->is_interactive() ) {
            if( $op == 'install' ) {
                $console = new console;
                $console->clear();
                $console->show_centered( \__appbase\lang('cli_welcome', 'bold+underlind()') )->lf();;
                $console->show_centered( \__appbase\lang('cli_cmsver', $this->app()->get_dest_version() ), 'bold' )->lf();
                $console->show_centered( \__appbase\lang('cli_hdr_op', $this->app()->get_op(), $this->app()->get_destdir() ) )->lf();
                $console->show_centered('----')->lf()->lf();

                $this->ask_questions( $console );
            } // install
        } // interactive
    }
} // class
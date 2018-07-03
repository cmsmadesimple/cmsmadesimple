<?php
namespace cms_autoinstaller\cli_step;
use cms_autoinstaller\cli_task;
use __appbase\console;
use __appbase\translator;

class step_4 extends cli_task
{
    protected function test_db_connection( $options )
    {
        // try a test connection
        $action = $this->app()->get_op();

        $spec = new \CMSMS\Database\ConnectionSpec;
        $spec->type = $options['db_type'];
        $spec->host = $options['db_hostname'];
        $spec->username = $options['db_username'];
        $spec->password = $options['db_password'];
        $spec->dbname = $options['db_name'];
        $spec->port = isset($options['db_port']) ? $options['db_port'] : null;
        $spec->prefix = $options['db_prefix'];
        $db = \CMSMS\Database\Connection::initialize($spec);
        $db->Execute("SET NAMES 'utf8'");

        // see if we can create and drop a table.
        try {
            $db->Execute('CREATE TABLE '.$options['db_prefix'].'_dummyinstall (i int)');
        }
        catch( \Exception $e ) {
            throw new \Exception(\__appbase\lang('error_createtable'));
        }

        try {
            $db->Execute('DROP TABLE '.$options['db_prefix'].'_dummyinstall');
        }
        catch( \Exception $e ) {
            throw new \Exception(\__appbase\lang('error_droptable'));
        }

        // see if a smatering of core tables exist
        if( $action == 'install' ) {
            try {
                $res = $db->GetOne('SELECT content_id FROM '.$options['db_prefix'].'content');
                if( $res > 0 ) throw new \Exception(\__appbase\lang('error_cli_cmstablesexist'));
            }
            catch( \CMSMS\Database\DatabaseException $e ) {
                // if this fails it's not a problem
            }

            try {
                $db->GetOne('SELECT module_name FROM '.$options['db_prefix'].'modules');
                if( $res > 0 ) throw new \Exception(\__appbase\lang('error_cli_cmstablesexist'));
            }
            catch( \CMSMS\Database\DatabaseException $e ) {
                // if this fails it's not a problem.
            }
        }
    }

    public function run()
    {
        // ask db credentials and php environment stuff
        $console = new console;
        $console->clear();
        $console->show_centered( \__appbase\lang('cli_welcome', 'bold+underlind()') )->lf();;
        $console->show_centered( \__appbase\lang('cli_cmsver', $this->app()->get_dest_version() ), 'bold' )->lf();
        $console->show_centered( \__appbase\lang('cli_hdr_op', $this->app()->get_op(), $this->app()->get_destdir() ) )->lf();
        $console->show_centered('----')->lf();

        $param_get = function( $arr, $key, $dflt = null ) {
            if( is_array( $arr ) && isset( $arr[$key]) ) return $arr[$key];
            return $dflt;
        };
        $op = $this->app()->get_op();

        // for installations ask this stuff
        // for upgrades and freshens,  since this is a CLI app  we assume we are secure enough to not have to confirm it';
        if( $op == 'install' ) {
            if( ! $this->app()->is_interactive() ) {
                die( 'incomplete at '.__FILE__.'::'.__LINE__ );
            }
            else {
                $options = $this->app()->get_options();
                // hardcode database type.
                $options['db_type'] = 'mysqli';

                $error = false;
                while( 1 ) {
                    if( $op == 'install' || $error ) {
                        // for install we absolutely require this information, so ask for it
                        // for freshen and upgrade.. we don't need it.

                        $console->lf();
                        $dflt = $param_get( $options, 'db_hostname', 'localhost' );
                        $options['db_hostname'] = $console->ask_string_cb(
                            function( $val, $console ) {
                                if( ! $val ) {
                                    $console->show("Database host name: ");
                                } else {
                                    $console->show("Database host name [$val]: ");
                                }
                            }, $dflt );

                        $dflt = $param_get( $options, 'db_name', '' );
                        $options['db_name'] = $console->ask_required_string_cb(
                            function( $val, $console ) {
                                if( ! $val ) {
                                    $console->show("> Database name: ");
                                } else {
                                    $console->show("> Database name [$val]: ");
                                }
                            }, $dflt );

                        $dflt = $param_get( $options, 'db_username', '' );
                        $options['db_username'] = $console->ask_required_string_cb(
                            function( $val, $console ) {
                                if( ! $val ) {
                                    $console->show("> Database user name: ");
                                } else {
                                    $console->show("> Database user name [$val]: ");
                                }
                            }, $dflt );

                        $dflt = $param_get( $options, 'db_password', '' );
                        $options['db_password'] = $console->ask_required_string_cb(
                            function( $val, $console ) {
                                if( ! $val ) {
                                    $console->show("> Database password: ");
                                } else {
                                    $console->show("> Database password [$val]: ");
                                }
                            }, $dflt );

                        $dflt = $param_get( $options, 'db_prefix', 'cms_' );
                        $options['db_prefix'] = $console->ask_string_cb(
                            function( $val, $console ) {
                                if( ! $val ) {
                                    $console->show("> Database table prefix: ");
                                } else {
                                    $console->show("> Database table_prefix [$val]: ");
                                }
                            }, $dflt );

                        if( !$options['db_prefix'] ) {
                            // should not get here, previous code prevents it... but meh.
                            $console->show( 'You have not entered a talbe prefix. This is not recommended. ', 'yellow' );
                            if( ! $console->ask_bool('> Are you sure? [y/N]') ) continue;
                        }
                    }

                    // now try a test connection
                    try {
                        $this->test_db_connection( $options );
                        // we passed
                        // todo: if tables exist, we throw a error
                        $error = false;
                        break;
                    }
                    catch( \Exception $e ) {
                        $console->show('ERROR: '.$e->Getmessage(), 'red_bg+white')->lf();
                        $error = true; // ask for the information next go round
                    }
                }
            }
        }
    }
} // class
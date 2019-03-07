<?php
namespace CMSMS\internal;
use CMSModule;
use CmsApp;

class MactEncoder
{
    const KEY = '_R';
    private $salt;
    private $generate_old_mact;

    public function __construct( CmsApp $app )
    {
        // note:  the salt must be site specific, but not filesystem specific
        //        to allow the same URL to work after a site move, or even on multidomain sites
        $this->salt = sha1( $app->get_site_identifier() );
        $this->generate_old_mact = $app->getConfig()['generate_old_mact'];
    }

    protected function get_salt()
    {
        return $this->salt;
    }

    protected function decode_encoded_mact($strict_request_type = true)
    {
        // Creates a MactInfo object from a secure request
        $var = null;
        if( $strict_request_type ) {
            if( $_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET[self::KEY]) ) {
                $var = $_GET[self::KEY];
            }
            else if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST[self::KEY]) ) {
                $var = $_POST[self::KEY];
            }
        } else {
            $var = $_REQUEST[self::KEY];
        }
        if( !$var ) return;

        $decoded = base64_decode($var);
        if( !$decoded ) return;

        list($sig,$data) = explode(':::',$decoded);
        if( sha1($data.$this->get_salt()) != $sig ) {
            cms_error('When attempting to decode a signed module action request, signatures did not validate');
        }
        else {
            $data = json_decode($data,TRUE);
            if( is_array($data) && isset($data['action']) && isset($data['module']) && isset($data['id']) ) {
                return MactInfo::from_array($data);
            }
            return $mact;
        }
    }

    public function decode_old_mact($strict_request_type = true)
    {
        // creates a MactInfo object from a request
        $var = null;
        if( $strict_request_type ) {
            if( $_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['mact']) ) {
                $var = $_GET['mact'];
            }
            else if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mact']) ) {
                $var = $_POST['mact'];
            }
        } else {
            $var = $_REQUEST['mact'];
        }
        if( !$var ) return;

        // get the mactinfo
        list($module,$id,$action,$inline) = explode(',',$var,4);
        $arr = null;
        $arr['module'] = trim($module);
        $arr['id'] = trim($id);
        $arr['action'] = trim($action);
        $arr['inline'] = cms_to_bool($inline);
        $input = $_REQUEST;
        if( $strict_request_type ) {
            if( $_SERVER['REQUEST_METHOD'] == 'GET' ) $input = $_GET;
            else if( $_SERVER['REQUEST_METHOD'] == 'POST' ) $input = $_POST;
        }
        foreach( $input as $key => $val ) {
            if( startswith($key,$arr['id']) ) {
                $key = substr($key,strlen($arr['id']));
                $arr['params'][$key] = $val;
            }
        }
        return MactInfo::from_array($arr);
    }

    public function decode($strict_request_type = true)
    {
        if( $this->encrypted_key_exists($strict_request_type) ) {
            return $this->decode_encoded_mact($strict_request_type);
        }
        return $this->decode_old_mact($strict_request_type);
    }

    protected function encrypted_key_exists($strict_request_type = true)
    {
        if( !$strict_request_type ) return isset($_REQUEST[self::KEY]);
        if( !isset($_SERVER['REQUEST_METHOD']) ) return false;
        if( $_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET[self::KEY]) ) return true;
        if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST[self::KEY]) ) return true;
    }

    public function old_mact_exists($strict_request_type = true)
    {
        if( !$strict_request_type ) return isset($_REQUEST['mact']);
        if( !isset($_SERVER['REQUEST_METHOD']) ) return false;
        if( $_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['mact']) ) return true;
        if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mact']) ) return true;
    }

    public function remove_old_mact_params()
    {
        $input =& $_REQUEST;
        if( isset($input['mact']) ) {
            $parts = explode(',',$input['mact'],4);
            $id = trim($parts[1]);
            foreach( $input as $key => &$val ) {
                if( startswith($key,$id) ) $val = null;
            }
            $input['mact'] = null;
        }
    }

    public function expand_secure_mact($strict_request_type = true)
    {
        // if we have a secure mact request, convert it into an old style mact request
        // only happens if the secure mact key issset and valid.
        if( $this->encrypted_key_exists($strict_request_type) ) {
            $mact = $this->decode_encoded_mact($strict_request_type);
            if( $mact ) {
                // repopulate mact.
                $mact_str = "{$mact->module},{$mact->id},{$mact->action},{$mact->inline}";
                $_REQUEST['mact'] = $mact_str;
                foreach( $mact->params as $key => $val ) {
                    $key = $mact->id.$key;
                    $_REQUEST[$key] = $val;
                }
            }
        }
    }

    protected function encode_to_secure_url(MactInfo $mact, array $extraparms = null)
    {
        // outputs a URL slug query string.
        $json = json_encode($mact);
        $sig = sha1($json.$this->get_salt());
        $str = self::KEY.'='.base64_encode($sig.':::'.$json);
        if( !empty($extraparams) ) {
            foreach( $extraparams as $key => $val ) {
                '&amp;'.$key.'='.rawurlencode(cms_htmlentities($val));
            }
        }
        return $str;
    }

    protected function encode_to_mact_url(MactInfo $mact, array $extraparms = null)
    {
        // encodes to old style mact url.
        $arr = null;
        $arr['mact'] = "{$mact->module},{$mact->id},{$mact->action},{$mact->inline}";
        $params = $mact->params;
        if( !empty($params) ) {
            foreach( $params as $key => $val ) {
                $key = "{$mact->id}{$key}";
                $arr[$key] = $val;
            }
        }
        if( !empty($extraparms) ) {
            foreach( $extraparms as $key => $val ) {
                $arr[$key] = $val;
            }
        }

        $out = null;
        $keys = array_keys($arr);
        for( $i = 0, $n = count($keys); $i < $n; $i++ ) {
            $key = $keys[$i];
            $val = $arr[$key];
            $out .= cms_htmlentities($key).'='.rawurlencode($val);
            /*
            if( $key == 'mact' ) {
                // special case for the mact param?
                $out .= $key.'='.$val;
            }
            else {
                $out .= cms_htmlentities($key).'='.rawurlencode($val);
            }
            */
            if( $i < $n - 1 ) $out .= '&amp;';
        }
        return $out;
    }

    public function encode_to_url(MactInfo $mact, array $extraparms = null)
    {
        if( $this->generate_old_mact ) return $this->encode_to_mact_url($mact, $extraparms);
        return $this->encode_to_secure_url($mact, $extraparms);
    }

    public function create_mactinfo($module, string $id, string $action, bool $inline = null, array $params = null) : MactInfo
    {
        $arr = null;
        if( is_object($module) && $module instanceof CMSModule ) {
            $arr['module'] = $module->GetName();
        } else {
            $arr['module'] = trim($module);
        }
        $arr['action'] = trim($action);
        $arr['id'] = trim($id);
        $arr['inline'] = $inline;
        if( !$inline || !$arr['id'] ) $arr['id'] = MactInfo::CNTNT01;
        if( !empty($params) ) {
            $excluded = ['assign','id','returnid','action','module'];
            $tmp = null;
            foreach($params as $key => $val) {
                if( startswith($key,'__') ) continue;
                if( in_array($key,$excluded) ) continue;
                $tmp[$key] = $val;
            }
            if( !empty($tmp) ) $arr['params'] = $tmp;
        }

        return MactInfo::from_array($arr);
    }
} // class

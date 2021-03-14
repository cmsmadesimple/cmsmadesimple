<?php
namespace CMSMS\internal;
use CMSMS\hook_manager;

// handles setting, getting, and rendering
class page_string_handler
{
    /**
     * @ignore
     */
    private $data;

    /**
     * @ignore
     */
    private $replacements;

    /**
     * @ignore
     */
    private $hook_flag;

    /**
     * @ignore
     */
    private $hook_manager;

    /**
     * Constructor
     */
    public function __construct( hook_manager $mgr )
    {
        $this->hook_manager = $mgr;
    }

    /**
     * @ignore
     */
    public function __get(string $key) : string
    {
        return $this->getv($key);
    }

    /**
     * @ignore
     */
    public function __set(string $key, string $val = null)
    {
        return $this->setv($key,$val);
    }

    /**
     * @ignore
     */
    public function __isset(string $key)
    {
        return $this->exists($key,$val);
    }

    /**
     * Get a previously set variable.
     * use it like: {$foo=$cms_pagestr->getv('foo')}
     *
     * @param string $key
     * @return string|null The variable output, if applicable.
     */
    public function getv(string $key)
    {
        $key = trim($key);
        if( array_key_exists($key,$this->data) ) return $this->data[$key];
    }

    /**
     * Set a variable.
     * Use it like: {cms_pagestr->set foo=bar some=thing}
     */
    public function set(array $parms)
    {
        foreach( $parms as $key => $val ) {
            $this->setv($key,$val);
        }
    }

    /**
     * Set a variable.
     * Use it like: {$cms_pagestr->setv('foo','bar')};
     *
     * @param string $key The name of the variable
     * @param string|null $val The value
     */
    public function setv(string $key, string $val = null)
    {
        $key = trim($key);
        if( is_null($val) || strlen($val) == 0 ) {
            if( isset($this->data[$key]) ) unset($this->data[$key]);
            return;
        }

        $this->data[$key] = $val;
    }

    /**
     * Test if a key has already been set using the set, or setv methods.
     *
     * @param string $key
     * @return bool
     */
    public function exists(string $key) : bool
    {
        $key = trim($key);
        return array_key_exists($key,$this->data);
    }

    /**
     * @ignore
     */
    protected function get_placeholder(string $key)
    {
        static $var;
        $var++;
        return "##cms_page_string::$key##$var##";
    }

    /**
     * Render a variable.
     * use it like: {cms_pagestr->render foo=bar}
     */
    public function render(array $params)
    {
        // ignore params
        $key = get_parameter_value($params,'key');
        $dflt = get_parameter_value($params,'dflt');
        if( $key ) return $this->renderv($key, $dflt);
    }

    /**
     * Render a variable.
     * use it like: {$cms_pagestr->renderv('foo','bar')}
     *
     * @param string $key
     * @param string $val
     * @return string a placeholder string that should be embedded into content.
     */
    public function renderv(string $key, string $dflt = null)
    {
        // add a postrender hook, if we haven't already
        if( !$this->hook_flag ) {
            $this->hook_manager->add_hook('Core::ContentPostRender', [ $this, 'postrender'] );
            $this->hook_flag = true;
        }

        $placeholder = $this->get_placeholder($key);
        $this->replacements[$placeholder] = $dflt;;
        return $placeholder;
    }

    /**
     * @ignore
     */
    public function postrender( array $params )
    {
        if( empty($this->replacements) ) return;
        if( !isset($params['content']) ) return;

        $from_arr = $to_arr = null;
        if( !empty($this->replacements) ) {
            foreach( $this->replacements as $placeholder => $val ) {
                // parse out the variable ($key) from the placeholder
                // if we have data for it, overwrite $val
                $parts1 = explode('##',$placeholder,3);
                $parts2 = explode('::',$parts1[1],2);
                $key = $parts2[1];
                if( isset($this->data[$key]) ) $val = $this->data[$key];
                $from_arr[] = $placeholder;
                $to_arr[] = $val;
            }
            $params['content'] = str_replace($from_arr,$to_arr,$params['content']);
        }
    }

} // class

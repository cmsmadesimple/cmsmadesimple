<?php

class news_article
{
    private static $_keys = array('id','author_id','title','content','summary','extra','news_url','postdate','startdate','enddate',
                                  'category_id','status','author','authorname','category','canonical','fields','fieldsbyname','customfieldsbyname',
                                  'useexp','returnid','params','file_location');
    private $_rawdata = array();
    private $_meta = array();
    private $_inparams = array();
    private $_inid = 'm1_';

    private function _getdata($key)
    {
        $res = null;
        if( isset($this->_rawdata[$key]) ) $res = $this->_rawdata[$key];
        return $res;
    }


    private function _getauthorinfo($author_id,$authorname = FALSE)
    {
        if( !isset($this->_meta['author']) ) {
            $mod = cms_utils::get_module('News');
            $this->_meta['author'] = $mod->Lang('anonymous');
            $this->_meta['authorname'] = $mod->Lang('unknown');
            if( $author_id > 0 ) {
                $userops = cmsms()->GetUserOperations();
                $theuser = $userops->LoadUserById($author_id);
                if( is_object($theuser) ) {
                    $this->_meta['author'] = $theuser->username;
                    $this->_meta['authorname'] = $theuser->firstname.' '.$theuser->lastname; // is there some locale way we can do this?
                }
            }
            else if( $author_id < 0 ) {
                $author_id *= -1;
                $feu = cms_utils::get_module('FrontEndUsers');
                if( $feu ) {
                    $uinfo = $feu->GetUserInfo($author_id);
                    if( $uinfo[0] ) $this->_meta['author'] = $uinfo[1]['username'];
                }
            }
        }
        if( $authorname ) return $this->_meta['authorname'];
        return $this->_meta['author'];
    }


    private function _get_returnid()
    {
        if( !isset($this->_meta['returnid']) ) {
            $mod = cms_utils::get_module('News');
            $tmp = $mod->GetPreference('detail_returnid',-1);
            if( $tmp <= 0 ) $tmp = ContentOperations::get_instance()->GetDefaultContent();
            $this->_meta['returnid'] = $tmp;
        }
        return $this->_meta['returnid'];
    }


    private function _get_canonical()
    {
        if( !isset($this->_meta['canonical']) ) {
            $tmp = $this->news_url;
            if( $tmp == '' ) {
                $aliased_title = munge_string_to_url($this->title);
                $tmp = 'news/'.$this->id.'/'.$this->returnid."/{$aliased_title}";
            }
            $mod = cms_utils::get_module('News');
            $canonical = $mod->create_url($this->_inid,'detail',$this->returnid,$this->params,false,false,$tmp);
            $this->_meta['canonical'] = $canonical;
        }
        return $this->_meta['canonical'];
    }


    private function _get_params()
    {
        $params = $this->_inparams;
        $params['articleid'] = $this->id;
        return $params;
    }


    public function set_linkdata($id,$params,$returnid = '')
    {
        if( $id ) $this->_inid = $id;
        if( is_array($params) ) $this->_inparams = $params;
        if( $returnid != '' ) $this->_meta['returnid'] = $returnid;
    }


    public function set_field(news_field $field)
    {
        if( !isset($this->_rawdata['fieldsbyname']) ) $this->_rawdata['fieldsbyname'] = array();
        $name = $field->name;
        $this->_rawdata['fieldsbyname'][$name] = $field;
    }


    public function unset_field($name)
    {
        if( isset($this->_rawdata['fieldsbyname']) ) {
            if( isset($this->_rawdata['fieldsbyname'][$name]) ) unset($this->_rawdata['fieldsbyname'][$name]);
            if( count($this->_rawdata['fieldsbyname']) == 0 ) unset($this->_rawdata['fieldsbyname']);
        }
    }


    public function __get($key)
    {
        switch( $key ) {
        case 'id':
        case 'author_id':
        case 'title':
        case 'content':
        case 'summary':
        case 'extra':
        case 'news_url':
        case 'postdate':       // db time format
        case 'startdate':      // db time format
        case 'enddate':        // db time format
        case 'create_date':    // db time format
        case 'modified_date':  // db time format
        case 'category_id':
        case 'status':
            return $this->_getdata($key);

        case 'file_location':
            $config = \cms_config::get_instance();
            $url = $config['uploads_url'].'/news/id'.$this->id;
            return $url;

        case 'author':
            // metadata.
            return $this->_getauthorinfo($this->author_id);

        case 'authorname':
            // metadata.
            return $this->_getauthorinfo($this->author_id,TRUE);

        case 'category':
            // metadata.
            return news_ops::get_category_name_from_id($this->category_id);

        case 'useexp':
            if( isset($this->_meta['useexp']) ) return $this->_meta['useexp'];
            return 0;

        case 'canonical':
            // metadata
            return $this->_get_canonical();
            break;

        case 'fields':
        case 'customfieldsbyname': // deprecated
        case 'fieldsbyname': // deprecated
            if( isset($this->_rawdata['fieldsbyname']) ) return $this->_rawdata['fieldsbyname'];
            break;

        case 'returnid':
            // metadata
            return $this->_get_returnid();

        case 'params':
            // metadata
            return $this->_get_params();

        default:
            // check if there is a field with this alias
            if( isset($this->_rawdata['fieldsbyname']) && is_array($this->_rawdata['fieldsbyname']) ) {
                foreach( $this->_rawdata['fieldsbyname'] as $fname => &$obj ) {
                    if( !is_object($obj) ) continue;
                    if( $key == $obj->alias ) return $obj->value;
                }
            }
            //throw new Exception('Requesting invalid data from News article object '.$key);
        }
    }


    public function __isset($key)
    {
        switch( $key )
        {
        case 'id':
        case 'author_id':
        case 'title':
        case 'content':
        case 'summary':
        case 'extra':
        case 'news_url':
        case 'category_id':
        case 'postdate':
        case 'startdate':
        case 'enddate':
        case 'fieldsbyname':
        case 'status':
            return isset($this->_rawdata[$key]);

        case 'customfieldsbyname': // deprecated
        case 'fields': // deprecated
            return isset($this->_rawdata['fieldsbyname']);

        case 'author':
        case 'authorname':
        case 'category':
        case 'canonical':
        case 'returnid':
        case 'params':
        case 'useexp':
            return true;

        case 'create_date':
        case 'modified_date':
            if( $this->id != '' ) return TRUE;
            break;

        default:
            throw new Exception('Requesting invalid data from News article object '.$key);
        }

        return FALSE;
    }


    public function __set($key,$value)
    {
        switch( $key ) {
        case 'id':
        case 'author_id':
        case 'title':
        case 'content':
        case 'summary':
        case 'extra':
        case 'news_url':
        case 'category_id':
            $this->_rawdata[$key] = $value;
            break;

        case 'status':
            $value = strtolower($value);
            if( $value != 'published' ) $value = 'draft';
            $this->_rawdata[$key] = $value;
            break;

        case 'useexp':
            // this is a different case as this doesn't get stored in the database
            $this->_meta['useexp'] = $value;
            break;

        case 'create_date':   // db time format
        case 'modified_date': // db time format
        case 'postdate':      // db time format
        case 'startdate':     // db time format
        case 'enddate':       // db time format
            if( is_int($value) ) {
                $db = cmsms()->GetDb();
                $value = $db->DbTimeStamp($value);
            }
            $this->_rawdata[$key] = $value;
            break;

        default:
            throw new Exception('Modifying invalid data in News article object '.$key);

        }
    }

}

?>
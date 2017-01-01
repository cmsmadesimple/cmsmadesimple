<?php
namespace FilePicker;

class Profile
{
    private $_data = [ 'id'=>null, 'name'=>null, 'create_date'=>null, 'modified_date'=>null ];
    private $_params = [ 'dir'=>null, 'file_extensions'=>null, 'show_thumbs'=>null, 'can_upload'=>null, 'can_delete'=>null ];

    public function __construct(array $in = null)
    {
        if( !is_array( $in ) ) return;

        foreach( $in as $key => $value ) {
            switch( $key ) {
            case 'id':
                $this->_data[$key] = (int) $value;
                break;
            case 'name':
                $this->_data[$key] = trim($value);
                break;
            case 'create_date':
            case 'modified_date':
                $this->_data[$key] = (int) $value;
                break;
            case 'data':
                if( is_array($value) && isset($value['dir']) ) {
                    $this->_params = $value;
                } else if( is_string($value) ) {
                    $this->_params = unserialize($value);
                }
            }
        }
    }

    public function __get($key)
    {
        switch( $key ) {
        case 'id':
            return (int) $this->_data[$key];

        case 'name':
            return trim($this->_data[$key]);

        case 'create_date':
        case 'modified_date':
            return (int) $this->_data[$key];

        case 'file_extensions':
        case 'dir':
            return trim($this->_params[$key]);

        case 'data':
            return serialize($this->_params);

        case 'show_thumbs':
        case 'can_upload':
        case 'can_delete':
            return (bool) $this->_params[$key];

        default:
            throw new \LogicException("$key is not a gettable member of ".__CLASS__);
        }
    }

    public function validate()
    {
        if( !$this->name ) throw new \RuntimeException( 'err_profile_name' );
    }

    public function withNewId( $new_id = null )
    {
        if( !is_null($new_id) ) {
            $new_id = (int) $new_id;
            if( $new_id < 1 ) throw new \LogicException('Invalid id passed to '.__METHOD__);
        }
        $obj = clone $this;
        $obj->_data['id'] = $new_id;
        $obj->_data['create_date'] = $obj->_data['modified_date'] = time();
        return $obj;
    }

    public function withParams( array $params )
    {
        $obj = clone( $this );
        foreach( $params as $key => $val ) {
            switch( $key ) {
            case 'name':
                $obj->_data[$key] = trim($val);
                break;

            case 'file_extensions':
            case 'dir':
                $obj->_params[$key] = trim($val);
                break;

            case 'show_thumbs':
            case 'can_upload':
            case 'can_delete':
                $obj->_params[$key] = (bool) $val;
                break;
            }
        }
        return $obj;
    }

    public function markModified()
    {
        $obj = clone $this;
        $obj->_data['modified_date'] = time();
        return $obj;
    }
} // end of class
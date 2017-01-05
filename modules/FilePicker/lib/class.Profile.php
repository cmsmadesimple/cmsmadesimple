<?php
namespace FilePicker;

class Profile extends \CMSMS\FilePickerProfile
{
    private $_data = [ 'id'=>null, 'name'=>null, 'create_date'=>null, 'modified_date'=>null ];

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
            default:
                $this->setValue( $key, $value );
                break;
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

        default:
            return parent::__get($key);
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

    public function overrideWith( array $params )
    {
        $obj = clone( $this );
        foreach( $params as $key => $val ) {
            switch( $key ) {
            case 'id':
                // cannot set a new id this way
                break;

            case 'name':
                $obj->_data[$key] = trim($val);
                break;

            default:
                $obj->setValue($key,$val);
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
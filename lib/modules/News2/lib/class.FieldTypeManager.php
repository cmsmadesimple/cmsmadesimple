<?php
namespace News2;
use News2;

class FieldTypeManager
{
    private $types;
    private $mod;

    public function __construct( News2 $mod )
    {
        $this->mod = $mod;
    }

    public function registerType( FieldType $type )
    {
        $name = $type->getName();
        if( !$name ) throw new \LogicException('A FieldType must have a unique name');
        if( isset($this->types[$name]) ) throw new \LogicException("A fieldtype named $name is already registered");
        $this->types[$name] = $type;
    }

    public function getList()
    {
        $out = null;
        if( $this->types ) {
            foreach( $this->types as $name => $obj ) {
                $out[get_class($obj)] = $name;
            }
        }
        return $out;
    }

    public function getByClass( string $class )
    {
        foreach( $this->types as $name => $obj ) {
            if( get_class($obj) == $class ) return $obj;
        }
    }

    public function getAll()
    {
        $out = null;
        if( $this->types ) {
            foreach( $this->types as $type ) {
                $out[get_class($type)] = $type;
            }
        }
        return $out;
    }
} // class
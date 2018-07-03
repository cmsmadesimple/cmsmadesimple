<?php
namespace __appbase;

class console
{
    private $_cols;
    private $_rows;

    protected static $ANSI_CODES = array(
        "off"        => 0,
        "bold"       => 1,
        "italic"     => 3,
        "underline"  => 4,
        "blink"      => 5,
        "inverse"    => 7,
        "hidden"     => 8,
        "black"      => 30,
        "red"        => 31,
        "green"      => 32,
        "yellow"     => 33,
        "blue"       => 34,
        "magenta"    => 35,
        "cyan"       => 36,
        "white"      => 37,
        "black_bg"   => 40,
        "red_bg"     => 41,
        "green_bg"   => 42,
        "yellow_bg"  => 43,
        "blue_bg"    => 44,
        "magenta_bg" => 45,
        "cyan_bg"    => 46,
        "white_bg"   => 47
    );

    public function __construct()
    {
        $this->get_screensize();
    }

    protected function set($str, $color)
    {
        if( !$color ) return $str ;
        $color_attrs = explode("+", $color);
        $ansi_str = "";
        foreach ($color_attrs as $attr) {
            $ansi_str .= "\033[" . self::$ANSI_CODES[$attr] . "m";
        }
        $ansi_str .= $str . "\033[" . self::$ANSI_CODES["off"] . "m";
        return $ansi_str;
    }

    protected function get_screensize()
    {
        $this->_cols = exec('tput cols');
        $this->_rows = exec('tput lines');
    }

    protected function parse_bool( $in )
    {
        $in = trim( strtolower($in) );
        if( strlen($in) == 0 ) return false;
        if( $in == '1' || $in == 'on' || $in == 'y' || $in == 'yes' || $in == 'true' ) return true;
        return false;
    }

    public function clear()
    {
        echo "\033[2J\033[0;0H";
        return $this;
    }

    public function lf()
    {
        echo "\n";
        return $this;
    }

    public function show_centered( $line, $color = null )
    {
        $str = trim($line);
        $padding = floor( ($this->_cols - strlen( $str )) / 2 );
        $out = str_repeat( ' ', $padding ). $this->set( $str, $color );
        fprintf(STDOUT,$out);
        return $this;
    }

    public function show( $line, $color = null )
    {
        if( $color ) $line = $this->set( $line, $color );
        fprintf(STDOUT,$line);
        return $this;
    }

    public function ask_string( $prompt, $color = null, $dflt = null )
    {
        // terminates chain
        // returns string or null

        echo $this->set( $prompt, $color );
        $line = fgets( STDIN );
        $line = trim( $line );
        if( $line ) return $line;
        return $dflt;
    }

    public function ask_string_cb( $prompt_fn, $dflt = null )
    {
        $prompt_fn( $dflt, $this );
        $line = fgets( STDIN );
        $line = trim( $line );
        if( $line ) return $line;
        return $dflt;
    }

    public function ask_required_string_cb( $prompt_fn, $dflt = null )
    {
        while( 1 ) {
            $prompt_fn( $dflt, $this );
            $line = fgets( STDIN );
            $line = trim( $line );
            if( $line ) return $line; // no validation
            if( !$line && $dflt ) return $dflt;
        }
    }

    public function ask_required_string( $prompt, $color = null, $fn = null, $dflt = null )
    {
        // terminates chain
        // returns string
        if( !$fn && $color && is_callable( $color ) ) {
            $fn = $color;
            $color = null;
        }

        while( 1 ) {
            echo $this->set( $prompt, $color );
            $line = fgets( STDIN );
            $line = trim( $line );
            if( $line ) {
                if( $fn ) {
                    $res = $fn( $line );
                    if( $res ) return $res;
                }
                return $line;
            }
        }
    }

    public function ask_bool( $prompt, $color = null, $fn = null )
    {
        // terminates chain
        // returns bool
        if( !$fn && $color && is_callable( $color ) ) {
            $fn = $color;
            $color = null;
        }

        while( 1 ) {
            echo $this->set( $prompt, $color );
            $line = fgets( STDIN );
            $line = trim($line);
            if( $line ) {
                $bool = $this->parse_bool( $line );
                if( $fn ) return $fn( $bool );
                return $bool;
            }
        }
    }
} // class

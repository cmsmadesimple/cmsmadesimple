<?php

namespace cms_autoinstaller;

final class utils
{
    private function __construct() {}

    // get the list of versions we can upgrade from.
    public static function get_upgrade_versions()
    {
        $app = \__appbase\get_app();
        $app_config = $app->get_config();
        $min_upgrade_version = $app_config['min_upgrade_version'];
        if( !$min_upgrade_version ) throw new \Exception(\__appbase\lang('error_invalidconfig'));

        $dir = $app->get_appdir().'/upgrade';
        if( !is_dir($dir) ) throw new \Exception(\__appbase\lang('error_internal','u100'));

        $dh = opendir($dir);
        $versions = array();
        if( !$dh ) throw new \Exception(\__appbase\lang('error_internal',712));
        while( ($file = readdir($dh)) !== false ) {
            if( $file == '.' || $file == '..' ) continue;
            if( is_dir($dir.'/'.$file) &&
                (is_file("$dir/$file/MANIFEST.DAT.gz") || is_file("$dir/$file/MANIFEST.DAT") || is_file("$dir/$file/upgrade.php")) ) {
                if( version_compare($min_upgrade_version, $file) <= 0 ) $versions[] = $file;
            }
        }
        closedir($dh);
        if( count($versions) ) {
            usort($versions,'version_compare');
            return $versions;
        }
    }

    public static function get_upgrade_changelog($version)
    {
        // it is not an error to not have a changelog file
        $app = \__appbase\get_app();
        $dir = $app->get_appdir()."/upgrade/$version";
        if( !is_dir($dir) ) throw new \Exception(\__appbase\lang('error_internal','u100'));
        $files = array('CHANGELOG.txt','CHANGELOG.TXT','changelog.txt');
        foreach( $files as $fn ) {
            if( is_file("$dir/$fn") ) {
                // convert text into some sort of html
                $tmp = @file_get_contents("$dir/$fn");
                $tmp = nl2br(wordwrap(htmlspecialchars($tmp),80));
                return $tmp;
            }
        }
    }

    public static function get_upgrade_readme($version)
    {
        // it is not an error to not have a readme file
        $app = \__appbase\get_app();
        $dir = $app->get_appdir()."/upgrade/$version";
        if( !is_dir($dir) ) throw new \Exception(\__appbase\lang('error_internal','u100'));
        $files = array('README.HTML.INC','readme.html.inc','README.HTML','readme.html');
        foreach( $files as $fn ) {
            if( is_file("$dir/$fn") ) return @file_get_contents("$dir/$fn");
        }
        if( is_file("$dir/readme.txt") ) {
            // convert text into some sort of html.
            $tmp = @file_get_contents("$dir/readme.txt");
            $tmp = nl2br(wordwrap(htmlspecialchars($tmp),80));
            return $tmp;
        }
    }
    
    public static function strftime(string $format, $timestamp = null, ?string $locale = null): string
    {
        if (null === $timestamp) {
            $timestamp = new \DateTime;
        }
        elseif (\is_numeric($timestamp)) {
            $timestamp = \date_create('@' . $timestamp);
        
            if ($timestamp) {
                $timestamp->setTimezone(new \DateTimezone(\date_default_timezone_get()));
            }
        }
        elseif (\is_string($timestamp)) {
            $timestamp = \date_create($timestamp);
        }
    
        if (!($timestamp instanceof \DateTimeInterface)) {
            throw new \InvalidArgumentException('$timestamp argument is neither a valid UNIX timestamp, a valid date-time string or a DateTime object.');
        }
    
        $locale = \substr((string) $locale, 0, 5);
    
        $intl_formats = [
          '%a' => 'EEE',	// An abbreviated textual representation of the day	Sun through Sat
          '%A' => 'EEEE',	// A full textual representation of the day	Sunday through Saturday
          '%b' => 'MMM',	// Abbreviated month name, based on the locale	Jan through Dec
          '%B' => 'MMMM',	// Full month name, based on the locale	January through December
          '%h' => 'MMM',	// Abbreviated month name, based on the locale (an alias of %b)	Jan through Dec
        ];
    
        $intl_formatter = function (\DateTimeInterface $timestamp, string $format) use ($intl_formats, $locale) {
            $tz = $timestamp->getTimezone();
            $date_type = \IntlDateFormatter::FULL;
            $time_type = \IntlDateFormatter::FULL;
            $pattern = '';
        
            // %c = Preferred date and time stamp based on locale
            // Example: Tue Feb 5 00:45:10 2009 for February 5, 2009 at 12:45:10 AM
            if ($format == '%c') {
                $date_type = \IntlDateFormatter::LONG;
                $time_type = \IntlDateFormatter::SHORT;
            }
            // %x = Preferred date representation based on locale, without the time
            // Example: 02/05/09 for February 5, 2009
            elseif ($format == '%x') {
                $date_type = \IntlDateFormatter::SHORT;
                $time_type = \IntlDateFormatter::NONE;
            }
            // Localized time format
            elseif ($format == '%X') {
                $date_type = \IntlDateFormatter::NONE;
                $time_type = \IntlDateFormatter::MEDIUM;
            }
            else {
                $pattern = $intl_formats[$format];
            }
        
            return (new \IntlDateFormatter($locale, $date_type, $time_type, $tz, null, $pattern))->format($timestamp);
        };
    
        // Same order as https://www.php.net/manual/en/function.strftime.php
        $translation_table = [
            // Day
            '%a' => $intl_formatter,
            '%A' => $intl_formatter,
            '%d' => 'd',
            '%e' => function ($timestamp) {
                return \sprintf('% 2u', $timestamp->format('j'));
            },
            '%j' => function ($timestamp) {
                // Day number in year, 001 to 366
                return \sprintf('%03d', $timestamp->format('z') + 1);
            },
            '%u' => 'N',
            '%w' => 'w',
        
            // Week
            '%U' => function ($timestamp) {
                // Number of weeks between date and first Sunday of year
                $day = new \DateTime(\sprintf('%d-01 Sunday', $timestamp->format('Y')));
                return \sprintf('%02u', 1 + ($timestamp->format('z') - $day->format('z')) / 7);
            },
            '%V' => 'W',
            '%W' => function ($timestamp) {
                // Number of weeks between date and first Monday of year
                $day = new \DateTime(\sprintf('%d-01 Monday', $timestamp->format('Y')));
                return \sprintf('%02u', 1 + ($timestamp->format('z') - $day->format('z')) / 7);
            },
        
            // Month
            '%b' => $intl_formatter,
            '%B' => $intl_formatter,
            '%h' => $intl_formatter,
            '%m' => 'm',
        
            // Year
            '%C' => function ($timestamp) {
                // Century (-1): 19 for 20th century
                return \floor($timestamp->format('Y') / 100);
            },
            '%g' => function ($timestamp) {
                return \substr($timestamp->format('o'), -2);
            },
            '%G' => 'o',
            '%y' => 'y',
            '%Y' => 'Y',
        
            // Time
            '%H' => 'H',
            '%k' => function ($timestamp) {
                return \sprintf('% 2u', $timestamp->format('G'));
            },
            '%I' => 'h',
            '%l' => function ($timestamp) {
                return \sprintf('% 2u', $timestamp->format('g'));
            },
            '%M' => 'i',
            '%p' => 'A', // AM PM (this is reversed on purpose!)
            '%P' => 'a', // am pm
            '%r' => 'h:i:s A', // %I:%M:%S %p
            '%R' => 'H:i', // %H:%M
            '%S' => 's',
            '%T' => 'H:i:s', // %H:%M:%S
            '%X' => $intl_formatter, // Preferred time representation based on locale, without the date
        
            // Timezone
            '%z' => 'O',
            '%Z' => 'T',
        
            // Time and Date Stamps
            '%c' => $intl_formatter,
            '%D' => 'm/d/Y',
            '%F' => 'Y-m-d',
            '%s' => 'U',
            '%x' => $intl_formatter,
        ];
    
        $out = \preg_replace_callback('/(?<!%)(%[a-zA-Z])/', static function ($match) use ($translation_table, $timestamp) {
            if ($match[1] == '%n') {
                return "\n";
            }
            elseif ($match[1] == '%t') {
                return "\t";
            }
        
            if (!isset($translation_table[$match[1]])) {
                throw new \InvalidArgumentException(\sprintf('Format "%s" is unknown in time format', $match[1]));
            }
        
            $replace = $translation_table[$match[1]];
        
            if (\is_string($replace)) {
                return $timestamp->format($replace);
            }
            else {
                return $replace($timestamp, $match[1]);
            }
        },                            $format);
    
        $out = \str_replace('%%', '%', $out);
        return $out;
    }
} // end of class

?>

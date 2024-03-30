<?php
#CMS - CMS Made Simple
#(c)2004-2015 by Ted Kulp (wishy@users.sf.net)
#Visit our homepage at: http://www.cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id: misc.functions.php 9861 2015-03-28 16:21:04Z calguy1000 $

/**
 * Miscellaneous support functions
 *
 * @package CMS
 * @license GPL
 */
# Global
namespace
{
  if(!\function_exists('gzopen') && \function_exists('gzopen64')){
    /**
     * Wrapper for gzopen in case it does not exist.
     * Some installs of PHP (after PHP 5.3 use a different zlib library, and therefore gzopen is not defined.
     * This method works past that.
     *
     * @since 2.0
     * @ignore
     */
    function gzopen( $filename , $mode , $use_include_path = 0 ) {
      return gzopen64($filename, $mode, $use_include_path);
    }
  }
}

namespace CMSMS
{
  
  /**
   * A few minor optimizations were made on our version justified by the possible intense use of the function
   * Our version is somewhat adapted to our needs, and has a fallback in case Intl extension is not available
   */
  
  /**
   * Locale-formatted strftime using \IntlDateFormatter (PHP 8.1 compatible)
   * This provides a cross-platform alternative to strftime() for when it will be removed from PHP.
   * Note that output can be slightly different between libc sprintf and this function as it is using ICU.
   *
   * Original use (a):
   *
   * Usage:
   * use function \PHP81_BC\strftime;
   * echo strftime('%A %e %B %Y %X', new \DateTime('2021-09-28 00:00:00'), 'fr_FR');
   *
   * Original use (b):
   * \setlocale('fr_FR.UTF-8', LC_TIME);
   * echo \strftime('%A %e %B %Y %X', strtotime('2021-09-28 00:00:00'));
   *
   * @param string      $format    Date format
   * @param null        $timestamp Timestamp
   * @param string|null $locale
   *
   * @return string
   * @author BohwaZ <https://bohwaz.net/>
   */
  function strftime(string $format, $timestamp = NULL, ?string $locale = NULL) : string
  {
    if(NULL === $timestamp)
    {
      $timestamp = new \DateTime;
    }
    else if(\is_numeric($timestamp))
    {
      $timestamp = \date_create('@' . $timestamp);
      
      if($timestamp)
      {
        $timestamp->setTimezone(new \DateTimezone(\date_default_timezone_get()));
      }
    }
    else if(\is_string($timestamp))
    {
      $timestamp = \date_create($timestamp);
    }
    
    if(!($timestamp instanceof \DateTimeInterface))
    {
      throw new \InvalidArgumentException(
        '$timestamp argument is neither a valid UNIX timestamp, a valid date-time string or a DateTime object.'
      );
    }
    
    if( \class_exists(\IntlDateFormatter::class) )
    {
      $locale = \substr((string)$locale, 0, 5);
      
      $intl_formats = [
        '%a' => 'EEE',  // An abbreviated textual representation of the day	Sun through Sat
        '%A' => 'EEEE',  // A full textual representation of the day	Sunday through Saturday
        '%b' => 'MMM',  // Abbreviated month name, based on the locale	Jan through Dec
        '%B' => 'MMMM',  // Full month name, based on the locale	January through December
        '%h' => 'MMM',  // Abbreviated month name, based on the locale (an alias of %b)	Jan through Dec
      ];
      
      $intl_formatter = function(\DateTimeInterface $timestamp, string $format) use ($intl_formats, $locale) {
        $tz        = $timestamp->getTimezone();
        $date_type = \IntlDateFormatter::FULL;
        $time_type = \IntlDateFormatter::FULL;
        $pattern   = '';
        
        // %c = Preferred date and time stamp based on locale
        // Example: Tue Feb 5 00:45:10 2009 for February 5, 2009 at 12:45:10 AM
        if($format == '%c')
        {
          $date_type = \IntlDateFormatter::LONG;
          $time_type = \IntlDateFormatter::SHORT;
        }
        // %x = Preferred date representation based on locale, without the time
        // Example: 02/05/09 for February 5, 2009
        else if($format == '%x')
        {
          $date_type = \IntlDateFormatter::SHORT;
          $time_type = \IntlDateFormatter::NONE;
        }
        // Localized time format
        else if($format == '%X')
        {
          $date_type = \IntlDateFormatter::NONE;
          $time_type = \IntlDateFormatter::MEDIUM;
        }
        else
        {
          $pattern = $intl_formats[$format];
        }
        
        return (new \IntlDateFormatter($locale, $date_type, $time_type, $tz, NULL, $pattern))->format($timestamp);
      };
      
      // Same order as https://www.php.net/manual/en/function.strftime.php
      $translation_table = [
        // Day
        '%a' => $intl_formatter,
        '%A' => $intl_formatter,
        '%d' => 'd',
        '%e' => function($timestamp) {
          return \sprintf('% 2u', $timestamp->format('j'));
        },
        '%j' => function($timestamp) {
          // Day number in year, 001 to 366
          return \sprintf('%03d', $timestamp->format('z') + 1);
        },
        '%u' => 'N',
        '%w' => 'w',
        
        // Week
        '%U' => function($timestamp) {
          // Number of weeks between date and first Sunday of year
          $day = new \DateTime(\sprintf('%d-01 Sunday', $timestamp->format('Y')));
          
          return \sprintf('%02u', 1 + ($timestamp->format('z') - $day->format('z')) / 7);
        },
        '%V' => 'W',
        '%W' => function($timestamp) {
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
        '%C' => function($timestamp) {
          // Century (-1): 19 for 20th century
          return \floor($timestamp->format('Y') / 100);
        },
        '%g' => function($timestamp) {
          return \substr($timestamp->format('o'), -2);
        },
        '%G' => 'o',
        '%y' => 'y',
        '%Y' => 'Y',
        
        // Time
        '%H' => 'H',
        '%k' => function($timestamp) {
          return \sprintf('% 2u', $timestamp->format('G'));
        },
        '%I' => 'h',
        '%l' => function($timestamp) {
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
      
      $out = \preg_replace_callback(
         '/(?<!%)(%[a-zA-Z])/', static function($match) use ($translation_table, $timestamp) {
        if($match[1] == '%n')
        {
          return "\n";
        }
        else if($match[1] == '%t')
        {
          return "\t";
        }
        
        if(!isset($translation_table[$match[1]]))
        {
          throw new \InvalidArgumentException(\sprintf('Format "%s" is unknown in time format', $match[1]));
        }
        
        $replace = $translation_table[$match[1]];
        
        if(\is_string($replace))
        {
          return $timestamp->format($replace);
        }
        else
        {
          return $replace($timestamp, $match[1]);
        }
      }, $format
      );
      
      $out = \str_replace('%%', '%', $out);
      
      return $out;
    }
    
    // Fallback to custom implementation
    $translation_table = [
      // Day
      '%a' => 'D',      // Abbreviated weekday name
      '%A' => 'l',      // Full weekday name
      '%d' => 'd',      // Day of the month (01 to 31)
      '%e' => 'j',      // Day of the month (1 to 31)
      '%j' => 'z',      // Day of the year (001 to 366)
      '%u' => 'N',      // ISO-8601 numeric representation of the day of the week (1 to 7, Monday is 1)
      '%w' => 'w',      // Numeric representation of the day of the week (0 to 6, Sunday is 0)
    
      // Week
      '%U' => 'W',      // Week number of the year (Sunday as the first day of the week) (00 to 53)
      '%V' => 'W',      // ISO-8601 week number of the year (Monday as the first day of the week) (01 to 53)
      '%W' => 'W',      // Week number of the year (Monday as the first day of the week) (00 to 53)
    
      // Month
      '%b' => 'M',      // Abbreviated month name
      '%B' => 'F',      // Full month name
      '%h' => 'M',      // Abbreviated month name
      '%m' => 'm',      // Month number (01 to 12)
    
      // Year
      '%C' => 'Y',      // Century (20 for 2020)
      '%g' => 'y',      // Last two digits of the year (00 to 99)
      '%G' => 'Y',      // The full four-digit year
      '%y' => 'y',      // Last two digits of the year (00 to 99)
      '%Y' => 'Y',      // The full four-digit year
    
      // Time
      '%H' => 'H',      // Hour in 24-hour format (00 to 23)
      '%k' => 'G',      // Hour in 24-hour format (0 to 23)
      '%I' => 'h',      // Hour in 12-hour format (01 to 12)
      '%l' => 'g',      // Hour in 12-hour format (1 to 12)
      '%M' => 'i',      // Minutes (00 to 59)
      '%p' => 'A',      // AM/PM designation
      '%P' => 'a',      // am/pm designation
      '%r' => 'h:i:s A', // Time in 12-hour format (hh:mm:ss AM/PM)
      '%R' => 'H:i',    // Time in 24-hour format (hh:mm)
      '%S' => 's',      // Seconds (00 to 59)
      '%T' => 'H:i:s',  // Time in 24-hour format (hh:mm:ss)
      '%X' => 'H:i:s',  // Time in 24-hour format (hh:mm:ss)
      '%z' => 'O',      // Timezone offset in hours and minutes (+0200)
      '%Z' => 'T',      // Timezone abbreviation or name (UTC)
    
      // Time and Date Stamps
      '%c' => 'r',      // Date and time representation
      '%D' => 'm/d/y',  // Date (mm/dd/yy)
      '%F' => 'Y-m-d',  // Date (yyyy-mm-dd)
      '%s' => 'U',      // Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)
      '%x' => 'm/d/Y',  // Date representation
    ];

    
    $formatted = \preg_replace_callback('/(?<!%)(%[a-zA-Z])/', function($match) use ($timestamp, $translation_table)
    {
      if($match[1] == '%n') { return "\n"; } else if($match[1] == '%t') { return "\t"; }
      
      if(!isset($translation_table[$match[1]]))
      {
        throw new \InvalidArgumentException(\sprintf('Format "%s" is unknown in time format', $match[1]));
      }
      
      $replace = $translation_table[$match[1]];
      
      if(\is_string($replace))
      {
        return $timestamp->format($replace);
      }
      else
      {
        return $replace($timestamp, $match[1]);
      }
    }, $format);
    
    $out = \str_replace('%%', '%', $formatted);
    
    return $out;
  }
}

#
# EOF
#

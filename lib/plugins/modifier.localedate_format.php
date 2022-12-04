<?php
/**
 * Smarty plugin
 * Type:     modifier
 * Name:     localedate_format
 * Purpose:  format date/time values
 *
 * @param mixed $datevar      input date-time string | timestamp | DateTime object
 * @param string $format      optional strftime() and/or date()-compatible format for output. Default '%b %e, %Y'
 * @param mixed $default_date optional date-time to use if $datevar is empty. Default ''
 *
 * @return string
 */

function smarty_modifier_localedate_format($datevar, $format = '%b %e, %Y', $default_date = '')
{
    if (empty($datevar)) {
        $datevar = $default_date;
    }
    if (empty($datevar)) {
        $st = time();
    } elseif (is_numeric($datevar)) {
        $st = (int)$datevar;
    } elseif ($datevar instanceof DateTime
      || (interface_exists('DateTimeInterface', false) && $datevar instanceof DateTimeInterface)
    ) {
        $st = $datevar->format('U');
    } else {
        $st = strtotime($datevar);
        if ($st === -1 || $st === false) {
            $st = time();
        }
    }

    $outfmt = localedate_adjust($format);
    $tmp = date($outfmt, $st);
    $text = preg_replace_callback_array(array(
        '~[\x01-\x08\x0e\x0f]~' => function($m) use($st) {
            return localedate_ise ($st, $m[0]);
        },
        '~\x11~' => function($m) use($st) { // two-digit century
            return floor(date('Y', $st) / 100);
        },
        '~\x12~' => function($m) use($st) { // week of year, per ISO8601
            return substr(date('o', $st), -2);
        },
        '~\x10~' => function($m) use($st) { // week of year, assuming the first Monday is day 0
             $n1 = date('Y', $st);
             $n2 = date('z', strtotime('first monday of january '.$n1));
             $n1 = date('z', $st);
             return floor(($n2-$n1) / 7) + 1;
         },
        '~\x13~' => function($m) use($st) { // week of year, assuming the first Sunday is day 0
            $n1 = date('Y', $st);
            $n2 = date('z', strtotime('first sunday of january '.$n1));
            $n1 = date('z', $st);
            return floor(($n2-$n1) / 7) + 1;
        }
    ), $tmp);

    return $text;
}

function localedate_adjust($fmt)
{
    if (!$fmt) {
        return $fmt;
    }
    $from = array(
    '%a', // \1
    '%A', // \2
    '%d',
    '%e',
    '%j',
    '%u',
    '%w',
    '%W', // \10
    '%b', // \3
    '%h', // \3
    '%B', // \4
    '%m',
    '%y',
    '%Y',
    '%D',
    '%F',
    '%x', // \6
    '%H',
    '%k',
    '%I',
    '%l',
    '%M',
    '%p', // \0e
    '%P', // \0f
    '%r',
    '%R',
    '%S',
    '%T',
    '%X', // \7
    '%z',
    '%Z',
    '%c', // \8
    '%s',
    '%n',
    '%t',
    '%%',
    '%C', // \11
    '%g', // \12
    '%G',
    '%U', // \13
    '%V',
    );

    $to = array(
    "\1",
    "\2",
    'd',
    'j', // interim
    'z',
    'N',
    'w',
    "\x10",
    "\3",
    "\3",
    "\4",
    'm',
    'y',
    'Y',
    'm/d/y',
    'Y-m-d',
    "\6",
    'H',
    'G',
    'h',
    'g',
    'i',
    "\x0e",
    "\x0f",
    'h:i:s A',
    'H:i',
    's',
    'H:i:s',
    "\7",
    'O',
    'T',
    "\x8",
    'U',
    "\n",
    "\t",
    '&#37;', // '%' chars are valid but may confuse e.g. Smarty date-munger
    "\x11",
    "\x12",
    'o',
    "\x13",
    'W',
    );
    if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
// TODO robustly derive values for Windows OS
/* see
https://docs.microsoft.com/en-us/cpp/c-runtime-library/reference/strftime-wcsftime-strftime-l-wcsftime-l?redirectedfrom=MSDN&view=msvc-170
re other uses of '#' modifier
*/
        $to[3] = '#d'; // per php.net: correctly relace %e on Windows
    }
    return str_replace($from, $to, $fmt);
}

function localedate_ise ($st, $mode)
{
    if (extension_loaded('Intl')) {
        $gCms = \CmsApp::get_instance();
        $config = $gCms->GetConfig();
        $zone = $config['timezone'];
        $dt = new DateTime('', new DateTimeZone($zone));
        $dt->setTimestamp($st);
        $locale = \CmsNlsOperations::get_current_language();
        switch ($mode) {
        case "\1": // short day name
            return datefmt_format_object($dt, 'EEE', $locale);
        case "\2": // normal day name
            return datefmt_format_object($dt, 'EEEE', $locale);
        case "\3": // short month name
            return datefmt_format_object($dt, 'MMM', $locale);
        case "\4": // normal month name
            return datefmt_format_object($dt, 'MMMM', $locale);
        case "\6": // date only
            return datefmt_format_object($dt,
                array(IntlDateFormatter::FULL, IntlDateFormatter::NONE), $locale);
        case "\7": // time only
            return datefmt_format_object($dt,
                array(IntlDateFormatter::NONE, IntlDateFormatter::MEDIUM), $locale);
        case "\x8": // date and time
            return datefmt_format_object($dt,
                array(IntlDateFormatter::FULL, IntlDateFormatter::MEDIUM), $locale);
        case "\x0e": // am/pm, upper-case
        case "\x0f": // am/pm, lower-case
            $s = datefmt_format_object($dt, 'a', $locale);
            if ($mode == "\x0e") {
                // force upper-case, any charset
                if (!preg_match('/[\x80-\xff]/',$s)) { return strtoupper($s); }
                elseif (function_exists('mb_strtoupper')) { return mb_strtoupper($s); }
            } else {
                // force lower-case, any charset
                if (!preg_match('/[\x80-\xff]/',$s)) { return strtolower($s); }
                elseif (function_exists('mb_strtolower')) { return mb_strtolower($s); }
            }
            return $s;
        default:
            return 'Unknown Format';
        }
    } elseif (function_exists('nl_langinfo')) { // not Windows OS
        switch ($mode) {
        case "\1": // short day name
            $n = date('w', $st) + 1;
            $fmt = constant('ABDAY_'.$n);
            return nl_langinfo($fmt);
        case "\2": // normal day name
            $n = date('w', $st) + 1;
            $fmt = constant('DAY_'.$n);
            return nl_langinfo($fmt);
        case "\3": // short month name
            $n = date('n', $st);
            $fmt = constant('ABMON_'.$n);
            return nl_langinfo($fmt);
        case "\4": // normal month name
            $n = date('n', $st);
            $fmt = constant('MON_'.$n);
            return nl_langinfo($fmt);
        case "\6": // date without time
            $fmt = nl_langinfo(D_FMT);
            $fmt = localedate_adjust($fmt);
            return date($fmt);
        case "\7": // time without date
            $fmt = nl_langinfo(T_FMT);
            $fmt = localedate_adjust($fmt);
            return date($fmt);
        case "\x8": // date and time
            $fmt = nl_langinfo(D_T_FMT);
            $fmt = localedate_adjust($fmt);
            return date($fmt);
        case "\x0e": // am/pm, upper-case
        case "\x0f": // am/pm, lower-case
            $s = date('A', $st);
            $fmt = ($s == 'AM') ? AM_STR : PM_STR;
            $s = nl_langinfo($fmt);
            if ($mode == "\x0e") {
                // force upper-case, any charset
                if (!preg_match('/[\x80-\xff]/',$s)) { return strtoupper($s); }
                elseif (function_exists('mb_strtoupper')) { return mb_strtoupper($s); }
            } else {
                // force lower-case, any charset
                if (!preg_match('/[\x80-\xff]/',$s)) { return strtolower($s); }
                elseif (function_exists('mb_strtolower')) { return mb_strtolower($s); }
            }
            return $s;
        default:
            return 'Unknown Format';
        }
    } else {
// TODO robustly derive values for Windows OS
        switch ($mode) {
        case "\1": // short day name
            return date('D', $st);
        case "\2": // normal day name
            return date('l', $st);
        case "\3": // short month name
            return date('M', $st);
        case "\4": // normal month name
            return date('F', $st);
        case "\6": // date only
            return date('j F Y', $st);
        case "\7": // time only
            return date('H:i:s', $st);
        case "\x8": // date and time
            return date('j F Y h:i a', $st);
        case "\x0e": // am/pm, upper-case
            return date('A', $st);
        case "\x0f": // am/pm, lower-case
            return date('a', $st);
        default:
            return 'Unknown Format';
        }
    }
}

function smarty_cms_help_modifier_localedate_format()
{
    echo <<<EOS
<p>Replacement for Smarty modifier date_format. This does not use deprecated strftime() to process the format</p>
<pre>{\$datetimevar|localedate_format[:&apos;optional params&apos;]}</pre>
<p>Parameters</p>
<ul>
<li>(<em>optional</em>)string PHP date()- and/or strftime()-compatible format specifier. Default &apos;%b %e, %Y&apos;</li>
<li>(<em>optional</em>)stamp|string|DateTime object default datetime specifier to use if necessary</li>
</ul>
EOS;
}

function smarty_cms_about_modifier_localedate_format()
{
    echo <<<EOS
<p>Change History:</p>
<ul>
 <li>None</li>
</ul>
EOS;
}

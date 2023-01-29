<?php
/**
 * Smarty plugin
 * Type:     modifier<br>
 * Name:     cms_date_format<br>
 * Purpose:  format datestamps via strftime<br>
 * Input:<br>
 *          - string: input date string
 *          - format: strftime format for output
 *          - default_date: default date if $string is empty
 *
 * @link   http://www.smarty.net/manual/en/language.modifier.date.format.php date_format (Smarty online manual)
 * @author Monte Ohrt <monte at ohrt dot com>
 *
 * @param string $string       input date string
 * @param null   $format       strftime format for output
 * @param string $default_date default date if $string is empty
 *
 * @return string |void
 * @uses   smarty_make_timestamp()
 *
 * Modified by JoMorg
 */
function smarty_modifier_cms_date_format($string, $format = NULL, $default_date = '', $formatter = 'auto')
{
	if(NULL === $format)
	{
		$format = get_site_preference('defaultdateformat');
		
		if('' === $format)
		{
			$format = '%b %e, %Y';
		}
		if(!CmsApp::get_instance()->is_frontend_request())
		{
			if($uid = get_userid(FALSE))
			{
				$tmp = get_preference($uid, 'date_format_string');
				if('' !== $tmp)
				{
					$format = $tmp;
				}
			}
		}
	}
	
	/**
	 * require_once the {@link shared.make_timestamp.php} plugin
	 */
	static $is_loaded = FALSE;
	if(!$is_loaded)
	{
		if(!is_callable('smarty_make_timestamp'))
		{
			include_once SMARTY_PLUGINS_DIR . 'shared.make_timestamp.php';
		}
		$is_loaded = TRUE;
	}
	if(!empty($string) && '0000-00-00' !== $string && '0000-00-00 00:00:00' !== $string)
	{
		$timestamp = smarty_make_timestamp($string);
	}
	else if(!empty($default_date))
	{
		$timestamp = smarty_make_timestamp($default_date);
	}
	else
	{
		return;
	}
	if('strftime' === $formatter || ('auto' === $formatter && FALSE !== strpos($format, '%')))
	{
		if(Smarty::$_IS_WINDOWS)
		{
			$_win_from = [
				'%D',
				'%h',
				'%n',
				'%r',
				'%R',
				'%t',
				'%T'
			];
			$_win_to   = [
				'%m/%d/%y',
				'%b',
				"\n",
				'%I:%M:%S %p',
				'%H:%M',
				"\t",
				'%H:%M:%S'
			];
			if(FALSE !== strpos($format, '%e'))
			{
				$_win_from[] = '%e';
				$_win_to[]   = sprintf('%\' 2d', date('j', $timestamp));
			}
			if(FALSE !== strpos($format, '%l'))
			{
				$_win_from[] = '%l';
				$_win_to[]   = sprintf('%\' 2d', date('h', $timestamp));
			}
			$format = str_replace($_win_from, $_win_to, $format);
		}
		
		return CMSMS\strftime($format, $timestamp);
	}
	
	return date($format, $timestamp);
}

// EOF
?>

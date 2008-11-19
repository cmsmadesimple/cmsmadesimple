<?php
//http://www.php.net/manual/en/function.html-entity-decode.php
function cms_html_entity_decode_utf8( $string, $convert_single_quotes = false )
{
	static $trans_tbl;
	//replace numeric entities
	$string = preg_replace('~&#x0*([0-9a-f]+);~ei', '_code2utf8(hexdec("\\1"))', $string);
	$string = preg_replace('~&#0*([0-9]+);~e', '_code2utf8(\\1)', $string);
	//replace literal entities
	if (!isset($trans_tbl))
	{
		$trans_tbl=array();
		foreach (get_html_translation_table(HTML_ENTITIES) as $val=>$key)
			$trans_tbl[$key] = utf8_encode($val);
	}
	$decode = strtr($string, $trans_tbl);
	if($convert_single_quotes) $decode = str_replace("'", "\'", $decode);

	return $decode;
}
//Returns the utf string corresponding to the unicode value (from php.net, courtesy - romans@void.lv)
function _code2utf8( $num )
{
	if($num < 0) return '';
	if($num < 128) return chr($num);

	//Removing / Replacing Windows Illegals Characters
	if($num < 160)
	{
		switch ($num)
		{
			case 128: $num=8364; break;
			case 129: $num=160;  break; //(Rayo:) #129 using no relevant sign, thus, mapped to the saved-space #160
			case 130: $num=8218; break;
			case 131: $num=402;  break;
			case 132: $num=8222; break;
			case 133: $num=8230; break;
			case 134: $num=8224; break;
			case 135: $num=8225; break;
			case 136: $num=710;  break;
			case 137: $num=8240; break;
			case 138: $num=352;  break;
			case 139: $num=8249; break;
			case 140: $num=338;  break;
			case 141: $num=160;  break; //(Rayo:) #129 using no relevant sign, thus, mapped to the saved-space #160
			case 142: $num=381;  break;
			case 143: $num=160;  break; //(Rayo:) #129 using no relevant sign, thus, mapped to the saved-space #160
			case 144: $num=160;  break; //(Rayo:) #129 using no relevant sign, thus, mapped to the saved-space #160
			case 145: $num=8216; break;
			case 146: $num=8217; break;
			case 147: $num=8220; break;
			case 148: $num=8221; break;
			case 149: $num=8226; break;
			case 150: $num=8211; break;
			case 151: $num=8212; break;
			case 152: $num=732;  break;
			case 153: $num=8482; break;
			case 154: $num=353;  break;
			case 155: $num=8250; break;
			case 156: $num=339;  break;
			case 157: $num=160;  break; //(Rayo:) #129 using no relevant sign, thus, mapped to the saved-space #160
			case 158: $num=382;  break;
			case 159: $num=376;  break;
		}
	}

	if ($num < 2048) return chr(($num >> 6) + 192) . chr(($num & 63) + 128);
	if ($num < 65536) return chr(($num >> 12) + 224) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
	if ($num < 2097152) return chr(($num >> 18) + 240) . chr((($num >> 12) & 63) + 128) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
	return '';
}
?>

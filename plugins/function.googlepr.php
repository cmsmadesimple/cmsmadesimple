<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://cmsmadesimple.sf.net
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

function smarty_cms_function_googlepr($params, &$smarty) {
	$domain = $_SERVER['SERVER_NAME'];
	if (!empty($params['domain']))
		$domain = $params['domain'];

	define('GMAG', 0xE6359A60); 

	//unsigned shift right 
	function zeroFill($a, $b){
		$z = hexdec(80000000); 
		if ($z & $a){ 
			$a = ($a>>1); 
			$a &= (~$z); 
			$a |= 0x40000000; 
			$a = ($a>>($b-1)); 
		}else{ 
			$a = ($a>>$b); 
		}
		return $a; 
	} 


	function mix($a,$b,$c) { 
		$a -= $b; $a -= $c; $a ^= (zeroFill($c,13)); 
		$b -= $c; $b -= $a; $b ^= ($a<<8); 
		$c -= $a; $c -= $b; $c ^= (zeroFill($b,13)); 
		$a -= $b; $a -= $c; $a ^= (zeroFill($c,12)); 
		$b -= $c; $b -= $a; $b ^= ($a<<16); 
		$c -= $a; $c -= $b; $c ^= (zeroFill($b,5)); 
		$a -= $b; $a -= $c; $a ^= (zeroFill($c,3)); 
		$b -= $c; $b -= $a; $b ^= ($a<<10); 
		$c -= $a; $c -= $b; $c ^= (zeroFill($b,15)); 

		return array($a,$b,$c); 
	} 

	function GCH($url, $length=null, $init=GMAG) { 
		if(is_null($length))
			$length = sizeof($url); 
		$a = $b = 0x9E3779B9; 
		$c = $init; 
		$k = 0; 
		$len = $length; 
		while($len >= 12) { 
			$a += ($url[$k+0] +($url[$k+1]<<8) +($url[$k+2]<<16) +($url[$k+3]<<24)); 
			$b += ($url[$k+4] +($url[$k+5]<<8) +($url[$k+6]<<16) +($url[$k+7]<<24)); 
			$c += ($url[$k+8] +($url[$k+9]<<8) +($url[$k+10]<<16)+($url[$k+11]<<24)); 
			$mix = mix($a,$b,$c); 
			$a = $mix[0]; $b = $mix[1]; $c = $mix[2]; 
			$k += 12; 
			$len -= 12; 
		} 

		$c += $length; 
		switch($len){ /* all the case statements fall through */ 
			case 11: $c+=($url[$k+10]<<24); 
			case 10: $c+=($url[$k+9]<<16); 
			case 9 : $c+=($url[$k+8]<<8); 
			/* the first byte of c is reserved for the length */ 
			case 8 : $b+=($url[$k+7]<<24); 
			case 7 : $b+=($url[$k+6]<<16); 
			case 6 : $b+=($url[$k+5]<<8); 
			case 5 : $b+=($url[$k+4]); 
			case 4 : $a+=($url[$k+3]<<24); 
			case 3 : $a+=($url[$k+2]<<16); 
			case 2 : $a+=($url[$k+1]<<8); 
			case 1 : $a+=($url[$k+0]); 
			/* case 0: nothing left to add */ 
		} 
		$mix = mix($a,$b,$c); 
		/*-------------------------------------------- report the result */ 
		return $mix[2]; 
	} 

		//converts a string into an array of integers containing the numeric value of the char 
	function strord($string) { 
		for($i=0;$i<strlen($string);$i++) { 
			$result[$i] = ord($string{$i}); 
		} 
		return $result; 
	} 

	function getPR($_url) { 
		$url = 'info:'.$_url; 
		$ch = GCH(strord($url)); 
		$url='info:'.urlencode($_url); 
		$pr = file("http://www.google.com/search?client=navclient-auto&ch=6$ch&ie=UTF-8&oe=UTF-8&features=Rank&q=$url"); 
		$pr_str = implode("", $pr); 
		return substr($pr_str,strrpos($pr_str, ":")+1); 
	} 

	return getPR($domain);
}

function smarty_cms_help_function_googlepr() {
  echo lang('help_function_googlepr');
}

function smarty_cms_about_function_googlepr() {
	?>
	<p>Author: Brett Batie&lt;brett-cms@classicwebdevelopment.com&gt;</p>
	<p>Version: 1.0</p>
	<p>
	Change History:<br/>
	None
	</p>
	<?php
}

?>

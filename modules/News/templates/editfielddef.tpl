{*
#CMS - CMS Made Simple
#(c)2004-6 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
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
#$Id$	
*}

<h3>{$title}</h3>
{$startform}
	<div class="pageoverflow">
		<p class="pagetext">*{$nametext}:</p>
		<p class="pageinput">{$inputname}</p>
	</div>
	{if $showinputtype eq true}
		<div class="pageoverflow">
			<p class="pagetext">*{$typetext}:</p>
			<p class="pageinput">{$inputtype}</p>
		</div>
	{else}
		{$inputtype}
	{/if}
	<div class="pageoverflow">
		<p class="pagetext">*{$maxlengthtext}:</p>
				<p class="pageinput">{$inputmaxlength}&nbsp;{$info_maxlength}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$userviewtext}:</p>
		<p class="pageinput">{$input_userview}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$hidden}{$submit}{$cancel}</p>
	</div>
{$endform}

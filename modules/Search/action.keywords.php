<?php
#---------------------------------------------------------------------------
# CMS Made Simple - Power for the professional, Simplicity for the end user.
# (c) 2004 - 2011 by Ted Kulp
# (c) 2011 - 2018 by the CMS Made Simple Development Team
# (c) 2018 and beyond by the CMS Made Simple Foundation
# This project's homepage is: https://www.cmsmadesimple.org
#---------------------------------------------------------------------------
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#---------------------------------------------------------------------------

if( !isset($gCms) ) exit;

$wordcount = 500;
if( isset($params['count']) ) $wordcount = (int)$params['count'];

$pageid = $returnid;
if( isset($params['pageid']) ) $pageid = (int)$params['pageid'];

$query = 'SELECT b.word
            FROM '.CMS_DB_PREFIX.'module_search_items a,
                 '.CMS_DB_PREFIX.'module_search_index b
           WHERE a.content_id = \''.$pageid.'\'
             AND a.module_name = \'search\'
             AND a.extra_attr = \'content\'
             AND a.id = b.item_id
           ORDER BY b.count DESC';

$dbr = $db->SelectLimit( $query, $wordcount, 0 );

$wordlist = array();
while( $dbr && ($row = $dbr->FetchRow() ) ) {
    $wordlist[] = $row['word'];
}
echo implode(',',$wordlist);

#
# EOF
#
<?php
#-------------------------------------------------------------------------
# Module: FilePicker - A CMSMS addon module to provide file picking capabilities.
# (c) 2016 by Fernando Morgado <jomorg@cmsmadesimple.org>
# (c) 2016 by Robert Campbell <calguy1000@cmsmadesimple.org>
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2006 by Ted Kulp (wishy@cmsmadesimple.org)
# This projects homepage is: http://www.cmsmadesimple.org
#-------------------------------------------------------------------------
#-------------------------------------------------------------------------
# BEGIN_LICENSE
#-------------------------------------------------------------------------
# This file is part of FilePicker
# FilePicker is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# FilePicker is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#-------------------------------------------------------------------------
# END_LICENSE
#-------------------------------------------------------------------------
use \FilePicker\ProfileDAO;

$db = $this->GetDb();
$taboptarray = array('mysql' => 'TYPE=MyISAM');
$dict = NewDataDictionary($db);

try {
    $flds = '
        id I AUTO PRIMARY KEY,
        name C(100) NOTNULL,
        data X,
        create_date I,
        modified_date i';

    $sqlarray = $dict->CreateTableSQL(ProfileDAO::table_name(), $flds, $taboptarray);
    $dict->ExecuteSQLArray($sqlarray);
    $sqlarray = $dict->CreateIndexSQL(CMS_DB_PREFIX.'cmsfp_idx0', ProfileDAO::table_name(), 'name', [ 'UNIQUE' ] );
    $dict->ExecuteSQLArray($sqlarray);
}
catch(Exception $e) {
    return $e->getMessage();
}

<?php

# Fix backend users' homepage url
# replace all previous secure params with [SECURITYTAG]
# remove the admin dir name from the url (url should be relative to admin dir)
$sql = 'SELECT user_id,value FROM '.CMS_DB_PREFIX.'userprefs WHERE preference = ?';
$homepages = $db->getAll($sql,['homepage'] );
if (is_array($homepages) && count($homepages))  {
    status_msg('Converting backend users\' homepage preference');

    $update_statement = 'UPDATE ' . CMS_DB_PREFIX . 'userprefs SET value = ? WHERE user_id = ? AND preference = ?';

    foreach ($homepages as $homepage) {

        
        $url = $homepage['value'];
        if (empty($url)) continue;
        
        // quick hacks to remove old secure param name from homepage url
        // and replace with the correct one.
        $url = str_replace('&amp;','&',$url);
        $tmp = explode('?',$url);
        @parse_str($tmp[1],$tmp2);
        if( in_array('_s_',array_keys($tmp2)) ) unset($tmp2['_s_']);
        if( in_array('sp_',array_keys($tmp2)) ) unset($tmp2['sp_']);
        
        $tmp2['_CMSKEY_'] = 'XXXX'; // current secure param
                
        foreach( $tmp2 as $k => $v ) {
            $tmp3[] = $k.'='.$v;
        }
        $url = $tmp[0].'?'.implode('&amp;',$tmp3);
        $url = str_replace('_CMSKEY_=XXXX','[SECURITYTAG]',$url);

        $url = preg_replace('@^/[^/]+/@','',$url); //remove admin folder from the url (if applicable)

        unset($tmp2,$tmp3);
        
        $db->execute($update_statement,[$url,$homepage['user_id'],'homepage']);
    }
}

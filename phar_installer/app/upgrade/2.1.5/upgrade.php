<?php
status_msg('Adding missing permissions');

//$gCms = cmsms();
$perms = array('Manage Stylesheets');
$all_perms = array();
foreach( $perms as $one_perm ) {
    $permission = new CmsPermission();
    $permission->source = 'Core';
    $permission->name = $one_perm;
    $permission->text = $one_perm;
    $permission->save();
    $all_perms[$one_perm] = $permission;
}

$groups = Group::load_all();
foreach( $groups as $group ) {
    if( strtolower($group->name == 'designer') ) {
        $group->GrantPermission('Manage Stylesheets');
    }
}

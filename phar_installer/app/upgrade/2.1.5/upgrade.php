<?php
status_msg('Adding missing permissions');

$perms = [ 'Manage Stylesheets' ];
$all_perms = array();
foreach( $perms as $one_perm ) {
    try {
       $permission = new CmsPermission();
       $permission->source = 'Core';
       $permission->name = $one_perm;
       $permission->text = $one_perm;
       $permission->save();
       $all_perms[$one_perm] = $permission;
    }
    catch( \Exception $e ) {
       // if it already exists, skip adding it to groups
       verbose_msg("Permission $one_perm already exists");
    }
}

$groups = Group::load_all();
foreach( $groups as $group ) {
    if( strtolower($group->name == 'designer') ) $group->GrantPermission('Manage Stylesheets');
}

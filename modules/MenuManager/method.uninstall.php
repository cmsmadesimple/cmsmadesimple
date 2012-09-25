<?php

// drop permissions
$this->RemovePermission('Manage Menu');

// drop preferences
$this->RemovePreference();

// drop event's
$this->RemoveEventHandler('Core','ContentEditPost');
$this->RemoveEventHandler('Core','ContentDeletePost');

// drop templates
$this->DeleteTemplate();

// remove plugin handlers
$this->RemoveSmartyPlugin();

?>
<?php
$module_name = trim($params['name']);
if( $name ) {
    $this->delete_jobs_by_module($module_name);
}

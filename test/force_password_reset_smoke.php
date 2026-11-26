<?php
/**
 * Smoke checks for force-password-reset implementation (no DB writes).
 * Run: php test/force_password_reset_smoke.php from cmsmadesimple root.
 */
$root = dirname(__DIR__);
$errors = 0;

$need = array(
	'lib/classes/internal/class.UserForcePasswordResetOps.inc.php',
	'lib/classes/internal/trait.UserOperationsListPermissions.inc.php',
	'lib/classes/class.useroperations.inc.php',
	'admin/login.php',
	'admin/edituser.php',
	'admin/templates/edituser.tpl',
	'admin/listusers.php',
	'admin/templates/listusers.tpl',
	'sql/apply_force_password_reset_schema_203.php',
);

foreach ($need as $rel) {
	$p = $root . '/' . $rel;
	if (!is_readable($p)) {
		fwrite(STDERR, "Missing: $rel\n");
		$errors++;
	}
}

$uo = file_get_contents($root . '/lib/classes/class.useroperations.inc.php');
if (strpos($uo, 'use UserOperationsListPermissionsTrait') === false) {
	fwrite(STDERR, "UserOperations must use UserOperationsListPermissionsTrait.\n");
	$errors++;
}
if (strpos($uo, 'SetForcePasswordReset') === false) {
	fwrite(STDERR, "SetForcePasswordReset missing from UserOperations.\n");
	$errors++;
}

$lang = file_get_contents($root . '/admin/lang/en_US.php');
if (strpos($lang, "event_desc_core::forcepasswordreset") === false) {
	fwrite(STDERR, "event_desc_core::forcepasswordreset missing from admin lang.\n");
	$errors++;
}

exit($errors > 0 ? 1 : 0);

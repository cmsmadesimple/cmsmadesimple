<?php
/**
 * One-time CLI: add force_password_reset columns and set schema version 203.
 * Run from public_html: php sql/apply_force_password_reset_schema_203.php
 */
if (php_sapi_name() !== 'cli') {
	http_response_code(404);
	header('Content-Type: text/plain; charset=utf-8');
	echo "CLI only.\n";
	exit(1);
}

$root = dirname(__DIR__);
$config = array();
if (!is_file($root . '/config.php')) {
	fwrite(STDERR, "config.php not found.\n");
	exit(1);
}
require $root . '/config.php';
if (empty($config['db_hostname']) || empty($config['db_username']) || !isset($config['db_password']) || empty($config['db_name'])) {
	fwrite(STDERR, "Incomplete database configuration.\n");
	exit(1);
}

$prefix = isset($config['db_prefix']) ? $config['db_prefix'] : 'cms_';
$prefix = preg_replace('/[^a-zA-Z0-9_]/', '', $prefix);
if ($prefix === '') {
	$prefix = 'cms_';
}

mysqli_report(MYSQLI_REPORT_OFF);
$m = @new mysqli($config['db_hostname'], $config['db_username'], $config['db_password'], $config['db_name']);
if ($m->connect_errno) {
	fwrite(STDERR, "Database connection failed.\n");
	exit(1);
}
$m->set_charset('utf8mb4');

function nt_column_exists(mysqli $m, $table, $col)
{
	$q = sprintf(
		"SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '%s' AND COLUMN_NAME = '%s'",
		$m->real_escape_string($table),
		$m->real_escape_string($col)
	);
	$r = $m->query($q);
	if (!$r) {
		return false;
	}
	$row = $r->fetch_row();
	return !empty($row[0]);
}

$users = $prefix . 'users';
$version = $prefix . 'version';

if (!nt_column_exists($m, $users, 'force_password_reset')) {
	$sql = "ALTER TABLE `{$users}` ADD COLUMN `force_password_reset` TINYINT(1) NOT NULL DEFAULT 0";
	if (!$m->query($sql)) {
		fwrite(STDERR, "ALTER force_password_reset failed: " . $m->error . "\n");
		exit(1);
	}
	echo "Added force_password_reset.\n";
} else {
	echo "Column force_password_reset already present.\n";
}

if (!nt_column_exists($m, $users, 'force_password_reset_reason')) {
	$sql = "ALTER TABLE `{$users}` ADD COLUMN `force_password_reset_reason` VARCHAR(255) NULL DEFAULT NULL";
	if (!$m->query($sql)) {
		fwrite(STDERR, "ALTER force_password_reset_reason failed: " . $m->error . "\n");
		exit(1);
	}
	echo "Added force_password_reset_reason.\n";
} else {
	echo "Column force_password_reset_reason already present.\n";
}

if (!$m->query("UPDATE `{$version}` SET version = '203'")) {
	fwrite(STDERR, "UPDATE version failed: " . $m->error . "\n");
	exit(1);
}
echo "Set {$version}.version to 203.\n";
echo "Done.\n";
exit(0);

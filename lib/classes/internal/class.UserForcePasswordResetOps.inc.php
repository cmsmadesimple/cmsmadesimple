<?php
/**
 * Force admin password reset on next login (CMSMS enhancement, GitHub issue #18).
 *
 * @package CMS
 * @license GPL
 * @internal
 */

/**
 * DB updates and hook for forced password reset.
 */
final class UserForcePasswordResetOps
{
	/**
	 * @param mixed $reason
	 * @return string
	 */
	private static function normalize_reason($reason)
	{
		if (!is_string($reason)) {
			return '';
		}
		$reason = trim($reason);
		if (strlen($reason) > 255) {
			$reason = substr($reason, 0, 255);
		}
		return $reason;
	}

	/**
	 * Invalidate UserOperations caches after user row change.
	 *
	 * @param int $user_id
	 * @return void
	 */
	private static function invalidate_caches($user_id)
	{
		$uo = UserOperations::get_instance();
		if (method_exists($uo, 'invalidateUserCaches')) {
			$uo->invalidateUserCaches((int) $user_id);
		}
	}

	/**
	 * Set force password reset flag (requires Manage Users for the acting user).
	 *
	 * @param int $user_id
	 * @param string $reason
	 * @param int|null $actor_uid
	 * @return bool
	 */
	public static function set_flag($user_id, $reason = '', $actor_uid = null)
	{
		$user_id = (int) $user_id;
		if ($user_id < 1) {
			return false;
		}
		if ($actor_uid === null) {
			if (!function_exists('get_userid')) {
				return false;
			}
			$actor_uid = (int) get_userid(false);
		} else {
			$actor_uid = (int) $actor_uid;
		}
		if ($actor_uid < 1 || !function_exists('check_permission') || !check_permission($actor_uid, 'Manage Users')) {
			return false;
		}

		$db = CmsApp::get_instance()->GetDb();
		$reason = self::normalize_reason($reason);
		$query = 'UPDATE ' . CMS_DB_PREFIX . 'users SET force_password_reset = 1, force_password_reset_reason = ? WHERE user_id = ?';
		$ok = $db->Execute($query, array($reason, $user_id));
		if ($ok === false) {
			return false;
		}
		self::invalidate_caches($user_id);
		\CMSMS\HookManager::do_hook('Core::ForcePasswordReset', array(
			'user_id' => $user_id,
			'reason' => $reason,
			'actor_uid' => $actor_uid,
		));
		audit($actor_uid, 'Core', 'Force password reset enabled for user_id ' . $user_id);
		return true;
	}

	/**
	 * Clear force password reset flag (callers must enforce authorization).
	 *
	 * @param int $user_id
	 * @return bool
	 */
	public static function clear_flag($user_id)
	{
		$user_id = (int) $user_id;
		if ($user_id < 1) {
			return false;
		}
		$db = CmsApp::get_instance()->GetDb();
		$query = 'UPDATE ' . CMS_DB_PREFIX . 'users SET force_password_reset = 0, force_password_reset_reason = NULL WHERE user_id = ?';
		$ok = $db->Execute($query, array($user_id));
		if ($ok === false) {
			return false;
		}
		self::invalidate_caches($user_id);
		return true;
	}
}

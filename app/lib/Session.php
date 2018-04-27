<?php
namespace Lib;
class Session {

	public static function start() {
		session_start();
	}

	public static function set($name, $value) {
		$_SESSION[$name] = $value;
	}

	public static function get($name) {
		if (isset($_SESSION[$name])) {
			return $_SESSION[$name];
		} else {
			return false;
		}

	}

	public static function del($name) {
		unset($_SESSION[$name]);
	}

	public function destroy() {
		$_SESSION = array();
		session_destroy();
	}

	public function save_prefs() {
		global $db, $auth;
		$prefs = serialize($this->prefs);
		$db->query("UPDATE condra_users SET prefs = '$prefs' WHERE id = '{$auth->id}'");
	}
}

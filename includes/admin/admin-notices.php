<?php class PopMake_Admin_Notice {
	protected static $messages = array();
	public static function init() {
		if ( !session_id() )
			session_start();
		//add_action('admin_notices',array('PopMake_Admin_Notice','render_notices'));
	}
	public static function get_messages($type = NULL) {
		self::init();
		if(empty($_SESSION[POPMAKE_SLUG.'_notices']))
			return array();
		$messages = $_SESSION[POPMAKE_SLUG.'_notices'];
		return $messages;
	}
	public static function render_notices() {
		self::init();
		foreach(PopMake_Admin_Notice::get_messages() as $key => $message) {
			?><div class="<?php esc_html_e($message['type']);?>">
				<p><?php esc_html_e($message['message']);?></p>
			</div><?php
			unset($_SESSION[POPMAKE_SLUG.'_notices'][$key]);
		}
	}
	public static function add($message, $type = 'updated') {
		self::init();
		if ( !session_id() ) session_start();
		$_SESSION[POPMAKE_SLUG.'_notices'][] = array(
			'message' => $message,
			'type' => $type
		);
	}
}

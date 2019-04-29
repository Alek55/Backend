<?php
	namespace app\lib;
	
	class Request {
		
		private $data;
		private static $sef = array();
		
		public function __construct() {
			$this->data = $this->xss(array_merge(self::$sef, $_REQUEST));
		}
		
		public static function addSef($arr) {
			self::$sef = $arr;
		}
		
		public function xss($data) {
			if(is_array($data)) {
				$escaped = array();
				foreach($data as $key => $value) {
					$escaped[$key] = $this->xss($value);
				}
				return $escaped;
			}
			return trim(htmlspecialchars($data));
		}
		
		public function __get($name) {
			if(isset($this->data[$name])) return $this->data[$name];
		}
		
	}
?>
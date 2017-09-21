<?php

class Context {
	/** @type resource */
	private $log;

	public function __construct(string $url=null, bool $append=True) {
		$this->set_log($url, $append);
	}

	public function set_log(string $url=null, bool $append=True) {
		if($url !== null) {
			$p = parse_url($url);
			if($p["scheme"] == "file") {
				if(@$p["host"] && @$p["path"]) {
					$path = $p["host"] . $p["path"];
				}
				elseif(@$p["host"]) {
					$path = $p["host"];
				}
				elseif(@$p["path"]) {
					$path = $p["path"];
				}
				else {
					throw new Exception("Filename not recognised");
				}
				$this->log = fopen($path, $append ? "a" : "w");
			}
			else {
				throw new Exception("Only file:// protocol is supported");
			}
		}
		else {
			$this->log = null;
		}
	}

	public function log_msg(string $func, string $text=null, string $type) {
		if($this->log) {
			fprintf(
				$this->log,
				"%f %s %d %d %s %s %s\n",
				microtime(true), # returning a float is 5.0+
				php_uname('n'),  # gethostname() is 5.3+
				posix_getpid(),
				function_exists("hphp_get_thread_id") ? hphp_get_thread_id() : posix_getpid(),
				$type, $func, $text
			);
		}
	}

	private function get_func() {
		$stack = debug_backtrace();
		if(count($stack) < 3) {
			return "top-level";
		}
		$p = $stack[2];
		return $p['function'];
	}

	public function log_bmark(string $text=null) {$this->log_msg($this->get_func(), $text, "BMARK");}
	public function log_clear(string $text=null) {$this->log_msg($this->get_func(), $text, "CLEAR");}
	public function log_endok(string $text=null) {$this->log_msg($this->get_func(), $text, "ENDOK");}
	public function log_ender(string $text=null) {$this->log_msg($this->get_func(), $text, "ENDER");}

	public function log_start(string $text=null, bool $bookmark=false, bool $clear=false) {
		if($clear) {
			$this->log_msg($this->get_func(), $text, "CLEAR");
		}
		if($bookmark) {
			$this->log_msg($this->get_func(), $text, "BMARK");
		}
		$this->log_msg($this->get_func(), $text, "START");
	}
}

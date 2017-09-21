<?php

require_once "context.php";

$ctx = null;

function hello() {
	global $ctx;
	$ctx->log_start("saying hello");
	printf("hello ");
	sleep(1);
	$ctx->log_endok();
}

function greet($name) {
	global $ctx;
	$ctx->log_start("greeting $name");
	printf("$name\n");
	sleep(2);
	$ctx->log_endok();
}

class ContextTestCase extends \PHPUnit\Framework\TestCase {
	public function testBasic() {
		global $ctx;
		$ctx = new Context("file://output.php.ctxt");
		$ctx->log_start("running program", true);
		hello();
		greet("world");
		$ctx->log_endok();

		$data = file_get_contents('output.php.ctxt');
		$this->assertContains("BMARK testBasic running program", $data);
		$this->assertContains("START testBasic running program", $data);
		$this->assertContains("START hello saying hello", $data);
		$this->assertContains("ENDOK hello", $data);
		$this->assertContains("START greet greeting world", $data);
		$this->assertContains("ENDOK greet", $data);
		$this->assertContains("ENDOK testBasic", $data);
	}
}

<?php

namespace PingMySlack\Tests\Interfaces;

use Mockery;
use WP_Mock\Tools\TestCase;
use PingMySlack\Interfaces\Kernel;

/**
 * @covers \PingMySlack\Interfaces\Kernel::__construct
 */
class KernelTest extends TestCase {
	public function setUp(): void {
		\WP_Mock::setUp();

		$this->kernel = $this->getMockForAbstractClass( Kernel::class );
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_register() {
		$this->kernel->expects( $this->once() )
			->method( 'register' );

		$this->kernel->register();

		$this->assertConditionsMet();
	}
}

class ConcreteClass implements Kernel {
	public function register(): void {
		echo 'Hello World!';
	}
}
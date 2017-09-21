<?php

use Bomoko\Lethaba\DIContainer;
use PHPUnit\Framework\TestCase;

class DIContainerTest extends TestCase
{
    /** @test */
    public function it_should_be_able_to_be_instantiated()
    {
        $container = new DIContainer();
        $this->assertInstanceOf(DIContainer::class, $container);
    }


    /** @test */
    public function it_should_accept_an_array_of_parameters_at_creation_time()
    {
        $container = new DIContainer(['a' => 'abc', 'b' => 123]);
        $this->assertEquals('abc', $container('a'));
        $this->assertEquals(123, $container('b'));
    }

    /** @test */
    public function it_should_allow_us_to_add_parameters_once_instantiated()
    {
        $container = new DIContainer();
        $container->bind('a', 'abc');
        $this->assertEquals('abc', $container('a'));
    }

    /** @test */
    public function it_should_throw_an_error_when_a_key_doesnt_exist()
    {
        $container = new DIContainer();
        $this->expectException(InvalidArgumentException::class);
        $output = $container('notBoundValue');
    }

    /** @test */
    public function it_should_run_an_invokable_passed_directly_into_bind()
    {
        $container = new DIContainer();
        $container->bind('invokable', function ($c) {
            return 'invoked';
        });
        $this->assertEquals('invoked', $container('invokable'));
    }

    /** @test */
    public function it_should_allow_invoked_functions_access_to_the_container_itself()
    {
        $container = new DIContainer();
        $container->bind('toBeAccessed', 'accessed');
        $container->bind('invoked', function ($c) {
          return $c('toBeAccessed');
        });

        $this->assertEquals('accessed', $container('invoked'));
    }

    /** @test */
    public function it_should_return_different_objects_on_each_invocation_if_service_not_defined_as_singleton()
    {
        $container = new DIContainer();
        $container->bind('service', function ($c) {
            return new stdClass();
        });

        $firstInvocation = $container('service');
        $secondInvocation = $container('service');
        $this->assertFalse($firstInvocation === $secondInvocation);
    }

    /** @test */
    public function it_should_bind_a_singleton_and_return_the_same_object_on_every_invocation()
    {
        $container = new DIContainer();
        $container->bindSingleton('singleton', function ($c) {
            return new stdClass();
        });

        $firstInvocation = $container('singleton');
        $secondInvocation = $container('singleton');
        $this->assertTrue($firstInvocation === $secondInvocation);
    }

    /** @test */
    public function it_should_allow_us_to_extend_a_service()
    {
        $container = new DIContainer();
        $container->bind('service', function ($c) {
            return "inside";
        });

        $container->extend('service', function ($innerResult, $c) {
          return "outside-" . $innerResult . "-outside";
        });

        $this->assertEquals('outside-inside-outside', $container('service'));
    }

    /** @test */
    public function it_should_protect_invokables_we_dont_want_to_be_run_automatically()
    {
        $container = new DIContainer();
        $container->protect('protectedInvokable', function () {
            return 'shouldRunOutsideContainer';
        });

        $runnable = $container('protectedInvokable');
        $this->assertTrue(is_callable($runnable));
        $this->assertEquals('shouldRunOutsideContainer', $runnable());

    }
}

<?php

use Bomoko\Lethaba\DIContainer;
use PHPUnit\Framework\TestCase;
use Bomoko\Lethaba\Exception\ContainerEntryNotFoundException;

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
        $this->assertEquals('abc', $container->get('a'));
        $this->assertEquals(123, $container->get('b'));
    }

    /** @test */
    public function it_should_allow_us_to_add_parameters_once_instantiated()
    {
        $container = new DIContainer();
        $container->bind('a', 'abc');
        $this->assertEquals('abc', $container('a'));
        $this->assertEquals('abc', $container->get('a'));
    }

    /** @test */
    public function it_should_throw_an_error_when_a_key_doesnt_exist()
    {
        $container = new DIContainer();
        $this->expectException(ContainerEntryNotFoundException::class);
        $output = $container('notBoundValue');
    }

    /** @test */
    public function it_should_throw_an_error_when_a_key_doesnt_exist_called_via_psr11_interface()
    {
        $container = new DIContainer();
        $this->expectException(ContainerEntryNotFoundException::class);
        $output = $container->get('notBoundValue');
    }

    /** @test */
    public function it_should_run_an_invokable_passed_directly_into_bind()
    {
        $container = new DIContainer();
        $container->bind('invokable', function ($c) {
            return 'invoked';
        });
        $this->assertEquals('invoked', $container('invokable'));
        $this->assertEquals('invoked', $container->get('invokable'));
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
        $this->assertEquals('accessed', $container->get('invoked'));
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
        $firstInvocation = $container->get('service');
        $secondInvocation = $container->get('service');
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
        $firstInvocation = $container->get('singleton');
        $secondInvocation = $container->get('singleton');
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
        $this->assertEquals('outside-inside-outside', $container->get('service'));
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
        $runnable = $container->get('protectedInvokable');
        $this->assertTrue(is_callable($runnable));
        $this->assertEquals('shouldRunOutsideContainer', $runnable());
    }

    /** @test */
    public function it_should_throw_an_exception_if_we_try_extend_a_service_that_doesnt_exist()
    {
        $container = new DIContainer();

        $this->expectException(ContainerEntryNotFoundException::class);
        $container->extend('service', function ($innerResult, $c) {
            return "outside-" . $innerResult . "-outside";
        });
    }

}

<?php
/**
 * Created by PhpStorm.
 * User: bomoko
 * Date: 2017/09/21
 * Time: 2:59 PM
 */

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

    
}

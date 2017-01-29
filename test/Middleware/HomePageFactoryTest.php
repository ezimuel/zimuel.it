<?php

namespace ZimuelTest\Action;

use Zimuel\Middleware\HomePage;
use Zimuel\Middleware\HomePageFactory;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class HomePageFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var ContainerInterface */
    protected $container;

    protected function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);

        $this->container->has(TemplateRendererInterface::class)->willReturn(true);
        $this->container
            ->get(TemplateRendererInterface::class)
            ->willReturn($this->prophesize(TemplateRendererInterface::class));

        $this->container->has('config')->willReturn(true);
        $this->container->get('config')->willReturn([ 'home' => [] ]);
    }

    public function testHomePageFactoryWithTemplate()
    {
        $factory = new HomePageFactory();
        $this->assertTrue($factory instanceof HomePageFactory);

        $homePage = $factory($this->container->reveal());
        $this->assertTrue($homePage instanceof HomePage);
    }
}

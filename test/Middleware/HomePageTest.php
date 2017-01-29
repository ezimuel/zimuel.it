<?php

namespace ZimuelTest\Middleware;

use Zimuel\Middleware\HomePage;
use Zimuel\Model\Post;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Diactoros\Response\HtmlResponse;

class HomePageTest extends \PHPUnit_Framework_TestCase
{
    protected $post;

    protected $config;

    protected function setUp()
    {
        $this->post = $this->prophesize(Post::class);
        $this->post->getItems(0,4)->willReturn([]);
        $this->config = [
            'posts'  => [],
            'events' => [],
            'book'   => ''
        ];
        $this->template = $this->prophesize(TemplateRendererInterface::class);
        $this->template->render('app::home', $this->config)->willReturn('');
    }

    public function testResponse()
    {
        $homePage = new HomePage($this->config, $this->post->reveal(), $this->template->reveal());
        $response = $homePage(new ServerRequest(['/']), new Response(), function () {
        });

        $this->assertTrue($response instanceof HtmlResponse);
    }
}

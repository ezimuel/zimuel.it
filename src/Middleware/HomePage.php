<?php
namespace Zimuel\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zimuel\Model\Post;

class HomePage
{
    private $post;

    private $template;

    public function __construct(array $config, Post $post, TemplateRendererInterface $template = null)
    {
        $this->config = $config;
        $this->post = $post;
        $this->template = $template;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $data = [
            'posts'  => $this->post->getItems(0,4),
            'events' => $this->config['events'] ?? '',
            'book'   => $this->config['book'] ?? ''
        ];
        return new HtmlResponse($this->template->render('app::home', $data));
    }
}

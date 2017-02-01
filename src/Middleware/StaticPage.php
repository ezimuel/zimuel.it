<?php
namespace Zimuel\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class StaticPage
{
    private $router;

    private $template;

    public function __construct(TemplateRendererInterface $template = null)
    {
        $this->template = $template;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $path = $request->getUri()->getPath();
        $path = substr($path, 1);
        return new HtmlResponse($this->template->render("app::$path"));
    }
}

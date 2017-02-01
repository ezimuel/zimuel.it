<?php
namespace Zimuel\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class Slide
{
    private $path;

    private $template;

    public function __construct(string $path, TemplateRendererInterface $template = null)
    {
        $this->path     = $path;
        $this->template = $template;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $file = $this->path . '/' . $request->getAttribute('conference');
        $talk = $request->getAttribute('talk');
        if (!empty($talk)) {
            $file .= '/' . $talk;
        }
        $file .= '.html';
        if (! file_exists($file)) {
          return $response->withStatus(404);
        }
        return new HtmlResponse(require $file);
    }
}

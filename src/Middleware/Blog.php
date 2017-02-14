<?php
namespace Zimuel\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zimuel\Model\Post;
use Zend\Paginator\Paginator;
use Zend\Expressive\Helper\UrlHelper;

class Blog
{
    private $helper;

    private $post;

    private $template;

    public function __construct(UrlHelper $helper, Post $post, TemplateRendererInterface $template = null)
    {
        $this->helper   = $helper;
        $this->post     = $post ;
        $this->template = $template;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $postName = $request->getAttribute('post');
        if (empty($postName)) {
            return $this->getAllPost($request, $response, $next);
        }

        $data = $this->post->getPost($postName);
        if (! $data) {
            return $next($request, $response->withStatus(404));
        }

        $data['content'] = $this->post->getPostContent($postName);
        return new HtmlResponse($this->template->render("app::post", $data));
    }

    public function getAllPost(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $paginator = new Paginator($this->post);
        $page = $request->getAttribute('page') ?? 1;
        $paginator->setCurrentPageNumber($page);
        $paginator->setDefaultItemCountPerPage(8);

        $pages = $paginator->getPages();
        $data = [ 'paginator' => $paginator ];
        if (isset($pages->next)) {
          $data['next'] = $this->helper->generate('blog-page', [ 'page' => $pages->next ]);
        }
        if (isset($pages->previous)) {
          $data['prev'] = $this->helper->generate('blog-page', [ 'page' => $pages->previous ]);
        }
        return new HtmlResponse($this->template->render("app::blog", $data));
    }
}

<?php
namespace Zimuel\Middleware;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zimuel\Model\Post;
use Zend\Expressive\Helper\UrlHelper;

class BlogFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $template = ($container->has(TemplateRendererInterface::class))
            ? $container->get(TemplateRendererInterface::class)
            : null;
        $helper = $container->get(UrlHelper::class);
        $post   = new Post('data/posts');

        return new Blog($helper, $post, $template);
    }
}

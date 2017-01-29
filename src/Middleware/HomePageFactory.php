<?php
namespace Zimuel\Middleware;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zimuel\Model\Post;

class HomePageFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $template = ($container->has(TemplateRendererInterface::class))
            ? $container->get(TemplateRendererInterface::class)
            : null;

        $config = $container->get('config');
        $post   = new Post('data/posts');

        return new HomePage($config['home'], $post, $template);
    }
}

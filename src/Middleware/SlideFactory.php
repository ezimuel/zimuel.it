<?php
namespace Zimuel\Middleware;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class SlideFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $template = ($container->has(TemplateRendererInterface::class))
            ? $container->get(TemplateRendererInterface::class)
            : null;

        $config = $container->get('config');
        if (!isset($config['path_slides']) || empty($config['path_slides'])) {
            throw new Exception\RuntimeException('The path_slides is empty or not defined');
        }

        return new Slide($config['path_slides'], $template);
    }
}

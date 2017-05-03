<?php
namespace Zimuel\Middleware;

use Interop\Container\ContainerInterface;
use Zimuel\Model\Post;
use Zend\Feed\Writer\Feed as ZFeed;
use Zend\Expressive\Helper\UrlHelper;

class FeedFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $post   = new Post('data/posts');
        $helper = $container->get(UrlHelper::class);

        $feed = new ZFeed();
        $feed->setTitle('Blog - Enrico Zimuel');
        $feed->setLink('http://www.zimuel.it/blog');
        $feed->setDescription('Blog - Enrico Zimuel');
        $feed->setFeedLink('http://www.zimuel.it/feed', 'rss');

        return new Feed($helper, $post, $feed);
    }
}

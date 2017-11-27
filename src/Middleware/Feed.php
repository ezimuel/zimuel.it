<?php
namespace Zimuel\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\TextResponse;
use Zimuel\Model\Post;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Feed\Writer\Feed as ZFeed;

class Feed
{
    public function __construct(UrlHelper $helper, Post $post, ZFeed $feed)
    {
        $this->helper = $helper;
        $this->post   = $post;
        $this->feed   = $feed;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $feedType   = 'rss';
        $feedUrl    = (string) $request->getUri();
        $blogUrl    = $this->helper->generate('blog');
        $postInFeed = 15;
        $this->feed->setLink('https://www.zimuel.it' . $blogUrl);
        $this->feed->setFeedLink($feedUrl, $feedType);
        $dateModified = false;

        foreach ($this->post->getItems(0, $postInFeed) as $url => $post) {
            $entry = $this->feed->createEntry();
            $entry->setTitle($post['title']);
            $entry->setLink('https://www.zimuel.it' . $blogUrl . '/' . $url);
            $entry->addAuthor([
                'name' => 'Enrico Zimuel'
            ]);
            $published = strtotime($post['date']);
            $entry->setDateCreated($published);
            // Get the updated date of the first blog post
            if (false === $dateModified) {
                $dateModified = $published;
            }
            $entry->setContent($this->post->getPostContent($url));
            $this->feed->addEntry($entry);
        }
        $this->feed->setDateModified($dateModified);
        $response = new TextResponse($this->feed->export($feedType));

        return $response->withHeader('Content-Type', sprintf('application/%s+xml', $feedType));
    }
}

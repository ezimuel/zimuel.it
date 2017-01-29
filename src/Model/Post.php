<?php

namespace Zimuel\Model;

use DOMDocument;
use Zend\Paginator\Adapter\AdapterInterface;

class Post implements AdapterInterface
{
    const CACHE = 'data/cache/posts.php';

    protected $posts;
    protected $path;

    public function __construct(string $path)
    {
        $this->path = $path;
        if (! file_exists(self::CACHE)) {
            $this->buildCache($path);
        }
        $this->posts = require self::CACHE;
    }

    /**
     * Get the number of posts
     *
     * @return integer
     */
    public function count()
    {
        return count($this->posts);
    }

    /**
     * Returns a collection of items for a page.
     *
     * @param  int $offset Page offset
     * @param  int $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        return array_slice($this->posts, $offset, $itemCountPerPage);
    }

    /**
     * Return the post if exist
     *
     * @return array|boolean
     */
    public function getPost(string $post)
    {
        return $this->posts[$post] ?? false;
    }

    /**
     * Return the post content if exist
     *
     * @return string|boolean
     */
    public function getPostContent(string $post)
    {
        if (! isset($this->posts[$post])) {
            return false;
        }
        $filename = sprintf("%s/%s.%s", $this->path, $post, $this->posts[$post]['type']);
        return file_get_contents($filename);
    }

    /**
     * Build the cache
     *
     * @param string $path
     */
    protected function buildCache($path)
    {
        $dom = new DOMDocument();
        // Get HTML posts
        foreach (glob($path . "/*.html") as $file) {
            libxml_use_internal_errors(true);
            $dom->loadHTMLFile($file);
            libxml_clear_errors();
            $post = $dom->getElementsByTagName('post');
            if (empty($post)) {
              continue;
            }
            $post = $post->item(0);
            $this->posts[pathinfo($file, PATHINFO_FILENAME)] = [
                'date'  => $post->getAttribute('date'),
                'title' => $post->getAttribute('title'),
                'sub'   => $post->getAttribute('sub') ?? '',
                'img'   => $post->getAttribute('img'),
                'type'  => 'html'
            ];
        }
        // reverse order by date
        uasort($this->posts, function($a, $b){
            return ($a['date'] <=> $b['date']) * -1;
        });
        file_put_contents(self::CACHE, '<?php return ' . var_export($this->posts, true) . ';', LOCK_EX);
    }
}

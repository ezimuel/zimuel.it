<?php
use Zend\Expressive\Application;
use Zend\Expressive\Helper;
use Zimuel\Middleware;

// Delegate static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server'
    && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
) {
    return false;
}

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

/** @var \Interop\Container\ContainerInterface $container */
$container = require 'config/container.php';

/** @var \Zend\Expressive\Application $app */
$app = $container->get(Application::class);

// Pipeline
$app->pipe(Helper\ServerUrlMiddleware::class);
$app->pipeRoutingMiddleware();
$app->pipe(Helper\UrlHelperMiddleware::class);
$app->pipeDispatchMiddleware();
$app->pipe(Helper\BodyParams\BodyParamsMiddleware::class);

// Routes
$app->get('/', Middleware\HomePage::class, 'home');
$app->get('/about-me', Middleware\StaticPage::class, 'about-me');
$app->get('/books', Middleware\StaticPage::class, 'books');
$app->get('/publications', Middleware\StaticPage::class, 'publications');
$app->get('/conferences', Middleware\StaticPage::class, 'conferences');
$app->get('/contact', Middleware\StaticPage::class, 'contact');
$app->get('/privacy-policy', Middleware\StaticPage::class, 'privacy');
$app->get('/blog/page/{page:\d+}', Middleware\Blog::class, 'blog-page');
$app->get('/blog[/{post}]', Middleware\Blog::class, 'blog');
$app->post('/send', Middleware\Send::class, 'send');
$app->get('/slides/{conference}[/{talk}]', Middleware\Slide::class, 'slide');
$app->get('/feed', Middleware\Feed::class, 'feed');

$app->raiseThrowables();
$app->run();

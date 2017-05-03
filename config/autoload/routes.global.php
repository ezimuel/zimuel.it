<?php

return [
    'dependencies' => [
        'invokables' => [
            Zend\Expressive\Router\RouterInterface::class => Zend\Expressive\Router\FastRouteRouter::class
        ],
        'factories' => [
            Zimuel\Middleware\StaticPage::class => Zimuel\Middleware\StaticPageFactory::class,
            Zimuel\Middleware\HomePage::class => Zimuel\Middleware\HomePageFactory::class,
            Zimuel\Middleware\Blog::class => Zimuel\Middleware\BlogFactory::class,
            Zimuel\Middleware\Send::class => Zimuel\Middleware\SendFactory::class,
            Zimuel\Middleware\Slide::class => Zimuel\Middleware\SlideFactory::class,
            Zimuel\Middleware\Feed::class => Zimuel\Middleware\FeedFactory::class
        ],
    ],
];

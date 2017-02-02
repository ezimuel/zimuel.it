<?php
namespace Zimuel\Middleware;

use Interop\Container\ContainerInterface;
use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

class SendFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');
        $httpClient = new GuzzleAdapter(new Client());
        if (!isset($config['sparkpost']['key']) || empty($config['sparkpost']['key'])) {
            throw new Exception\RuntimeException('The SparkPost API config key is empty or not defined');
        }
        $sparkPost = new SparkPost($httpClient, [ 'key' => $config['sparkpost']['key'] ]);

        if (!isset($config['recaptcha']['secret'])) {
            throw new Exception\RuntimeException('The reCaptcha secret key is empty or not defined');
        }
        return new Send($config['recaptcha']['secret'], $sparkPost);
    }
}

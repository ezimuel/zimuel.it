<?php
namespace Zimuel\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonlResponse;
use SparkPost\SparkPost;

class Send
{
    private $sparkPost;

    public function __construct(SparkPost $sparkPost)
    {
        $this->sparkPost = $sparkPost;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $data = $request->getParsedBody();
        if (empty($data['name'])) {
            return $response->withStatus(400, 'The name value cannot be empty');
        }
        if (empty($data['email'] || !filter_var($data['email'], FILTER_VALIDATE_EMAIL))) {
            return $response->withStatus(400, 'The email value must be a valid email address');
        }
        if (empty($data['message'])) {
            return $response->withStatus(400, 'The message value cannot be empty');
        }
        $promise = $this->sparkPost->transmissions->post([
            'content' => [
                'from' => [
                    'name' => $data['name'],
                    'email' => 'no-reply@zimuel.it',
                ],
                'subject' => '[zimuel.it] Message from website',
                'text' => $data['message'],
                'reply_to' => $data['email']
            ],
            'recipients' => [
                [
                    'address' => [
                        'name' => 'Enrico Zimuel',
                        'email' => 'enrico@zimuel.it',
                    ],
                ],
            ],
        ]);

        try {
            $response = $promise->wait();
            echo $response->getStatusCode()."\n";
            print_r($response->getBody())."\n";
        } catch (\Exception $e) {
            echo $e->getCode()."\n";
            echo $e->getMessage()."\n";
        }
        exit;
        return new JsonResponse();
    }
}

<?php
namespace Zimuel\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonlResponse;
use SparkPost\SparkPost;
use ReCaptcha\ReCaptcha;

class Send
{
    // reCaptcha secret
    private $secret;

    private $sparkPost;

    public function __construct(string $secret, SparkPost $sparkPost)
    {
        $this->sparkPost = $sparkPost;
        $this->secret    = $secret;
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
        if (empty($data['g-recaptcha-response'])) {
            return $response->withStatus(400, 'The reCaptcha response is empty');
        }

        $recaptcha = new ReCaptcha($this->secret);
        $result = $recaptcha->verify($gRecaptchaResponse, $_SERVER['REMOTE_ADDR']);
        if (!$result->isSuccess()) {
            return $response->withStatus(400, 'The captcha code is failing!');
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
            $result = $promise->wait();
            return $response->withStatus($result->getStatusCode());
        } catch (\Exception $e) {
            return $response->withStatus(500);
        }
    }
}

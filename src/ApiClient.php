<?php

namespace EasyAtWork\OpenWebUi;

use Exception;
use EasyAtWork\OpenWebUi\Traits\StaticConstructor;
use EasyAtWork\OpenWebUi\Traits\UnsafeOptions;

class ApiClient extends HttpClient
{
    use StaticConstructor;
    use UnsafeOptions;

    const OPTION_BASE_URI = 'baseUri';

    /** @var string */
    protected $baseUri;

    /** @var array<string, string> */
    protected $headers = [
        'User-Agent' => 'EaW-Chat-Client',
        'Content-Type' => 'application/json',
    ];

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->absorbOptions($options);

        if (!array_key_exists('baseUri', $this->guzzleOptions)) {
            $this->guzzleOptions['baseUri'] = $this->baseUri;
        }
    }

    /**
     * @param string $token
     * @return $this
     */
    public function useAuthorizationToken(string $token): self
    {
        $this->headers['Authorization'] = 'Bearer ' . $token;

        return $this;
    }

    /**
     * @param string $email
     * @param string $password
     * @return string
     * @throws Exception
     */
    public function signin(string $email, string $password): string
    {
        $response = $this->post('/api/v1/auths/signin', [], [
            'email' => $email,
            'password' => $password,
        ]);

        $this->useAuthorizationToken($response['token']);

        return $response['token'];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function models(): array
    {
        return $this->get('/api/models')['data'];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function documents(): array
    {
        return $this->get('/api/v1/documents/');
    }

    /**
     * @param array $chat
     * @return array
     * @throws Exception
     */
    public function processChat(array $chat): array
    {
        return $this->post('/ollama/api/chat', [], $chat);
    }

    /**
     * @param array $chat
     * @return array
     * @throws Exception
     */
    public function postProcessChat(array $chat): array
    {
        return $this->post('/api/chat/completed', [], $chat);
    }
}

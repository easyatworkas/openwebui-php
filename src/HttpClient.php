<?php

namespace EasyAtWork\OpenWebUi;

use Exception;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\GuzzleException;

abstract class HttpClient
{
    /** @var array<string, mixed> */
    protected $guzzleOptions = [];

    /** @var Guzzle */
    protected $guzzle;

    /** @var array<string, string> */
    protected $headers = [];

    /**
     * @return Guzzle
     */
    protected function guzzle(): Guzzle
    {
        if (!$this->guzzle || $this->guzzleOptions) {
            $this->guzzle = new Guzzle($this->guzzleOptions);

            $this->guzzleOptions = [];
        }

        return $this->guzzle;
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $parameters
     * @param array $data
     * @return array
     * @throws Exception
     */
    protected function request(string $method, string $endpoint, array $parameters = [], array $data = []): array
    {
        try {
            $response = $this->guzzle()->request(
                $method,
                $this->baseUri . $endpoint . '?' . http_build_query($parameters),
                [
                    'headers' => $this->headers,
                    'body' => json_encode($data),
                ]
            );
        } catch (GuzzleException $exception) {
            // Just wrap the exception to avoid exposing Guzzle classes directly.
            throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }

        return json_decode($response->getBody(), true);
    }

    /**
     * @param string $endpoint
     * @param array $parameters
     * @param array $data
     * @return array
     * @throws Exception
     */
    protected function get(string $endpoint, array $parameters = [], array $data = []): array
    {
        return $this->request('get', $endpoint, $parameters, $data);
    }

    /**
     * @param string $endpoint
     * @param array $parameters
     * @param array $data
     * @return array
     * @throws Exception
     */
    protected function post(string $endpoint, array $parameters = [], array $data = []): array
    {
        return $this->request('post', $endpoint, $parameters, $data);
    }

    /**
     * @param string $endpoint
     * @param array $parameters
     * @param array $data
     * @return array
     * @throws Exception
     */
    protected function put(string $endpoint, array $parameters = [], array $data = []): array
    {
        return $this->request('put', $endpoint, $parameters, $data);
    }

    /**
     * @param string $endpoint
     * @param array $parameters
     * @param array $data
     * @return array
     * @throws Exception
     */
    protected function delete(string $endpoint, array $parameters = [], array $data = []): array
    {
        return $this->request('delete', $endpoint, $parameters, $data);
    }
}

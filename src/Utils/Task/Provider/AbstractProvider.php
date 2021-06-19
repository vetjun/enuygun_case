<?php


namespace App\Utils\Task\Provider;


use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractProvider implements ProviderInterface
{
    protected HttpClientInterface $httpClient;
    /**
     * @var array|mixed
     */
    protected $params;

    public function __construct($params = [])
    {
        $this->params = $params;
        $this->httpClient = HttpClient::create();
    }

    /**
     * @return array|null
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function fetchTasks(): ?array
    {
        $response = $this->httpClient->request('GET', $this->getEndpoint());
        $statusCode = $response->getStatusCode();
        if (!($statusCode >= 200 && $statusCode < 300)) {
            throw new \RuntimeException('There was an error while fetching tasks from provider -> ' . $this->getName());
        }
        try {
            return $response->toArray();
        } catch (\Exception) {
            throw new \RuntimeException('Json data was expected from provider -> ' . $this->getName());
        }

    }

    public function getEndpoint(): ?string
    {
        return $this->endpoint;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
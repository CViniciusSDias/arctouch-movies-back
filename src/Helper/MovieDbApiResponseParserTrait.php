<?php


namespace App\Helper;

use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

trait MovieDbApiResponseParserTrait
{
    /** @var ClientInterface */
    private $httpClient;
    /** @var ParameterBagInterface */
    private $parameterPag;

    private function fetchResponseData(string $url): array
    {
        $response = $this->httpClient->request('GET', $url);
        $responseBody = $response->getBody();
        $responseData = json_decode($responseBody, true);

        if ($response->getStatusCode() !== 200) {
            throw new \DomainException($responseData['status_message'], $response->getStatusCode());
        }

        return $responseData;
    }

    private function assembleApiUrl(string $path, ?array $queryParams = null): string
    {
        $apiEndpoint = $this->parameterPag->get('api_url');
        $defaultParams = [
            'api_key' => $this->parameterPag->get('api_key'),
            'language' => 'en-US',
        ];
        $queryParams = array_merge($defaultParams, $queryParams ?? []);

        $url = $apiEndpoint . $path . '?' . http_build_query($queryParams);

        return $url;
    }
}

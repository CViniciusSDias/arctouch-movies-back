<?php


namespace App\Helper;

use GuzzleHttp\ClientInterface;

trait ResponseParserTrait
{
    /** @var ClientInterface */
    private $httpClient;

    private function fetchResponseData(string $url): array
    {
        $response = $this->httpClient->request('GET', $url);
        $responseBody = (string)$response->getBody();
        $responseData = json_decode($responseBody, true);

        if ($response->getStatusCode() !== 200) {
            throw new \DomainException($responseData['status_message'], $response->getStatusCode());
        }

        return $responseData;
    }

    private function assembleApiUrl(string $path, ?array $queryParams = null): string
    {
        $apiEndpoint = 'https://api.themoviedb.org/3';
        $apiKey = '1f54bd990f1cdfb230adb312546d765d';
        $defaultParams = [
            'api_key' => $apiKey,
            'language' => 'en-US',
        ];
        $queryParams = array_merge($defaultParams, $queryParams ?? []);

        $url = $apiEndpoint . $path . '?' . http_build_query($queryParams);

        return $url;
    }
}

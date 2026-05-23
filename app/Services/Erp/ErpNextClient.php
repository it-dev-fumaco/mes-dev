<?php

namespace App\Services\Erp;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * HTTP client for ERPNext / Frappe REST API (v15).
 *
 * Uses v1 endpoints (/api/resource, /api/method) which remain supported in Frappe 15.
 */
class ErpNextClient
{
    /** @var Client */
    protected $client;

    /** @var string */
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('ERP_API_BASE_URL', ''), '/');
        $apiKey = env('ERP_API_KEY', '');
        $apiSecret = env('ERP_API_SECRET', '');

        if ($this->baseUrl === '' || $apiKey === '' || $apiSecret === '') {
            throw new Exception('ERP API is not configured. Set ERP_API_BASE_URL, ERP_API_KEY, and ERP_API_SECRET.');
        }

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => 'token ' . $apiKey . ':' . $apiSecret,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'http_errors' => false,
            'timeout' => 120,
        ]);
    }

    /**
     * @param string $doctype e.g. "Stock Entry"
     * @param array $data
     * @return array decoded response data (includes "data" key with document)
     */
    public function createResource($doctype, array $data)
    {
        $encodedDoctype = rawurlencode($doctype);
        $response = $this->client->post('/api/resource/' . $encodedDoctype, [
            'json' => $data,
        ]);

        return $this->parseResponse($response->getStatusCode(), (string) $response->getBody());
    }

    /**
     * @param string $doctype
     * @param string $name
     * @return array
     */
    public function getResource($doctype, $name)
    {
        $encodedDoctype = rawurlencode($doctype);
        $encodedName = rawurlencode($name);
        $response = $this->client->get('/api/resource/' . $encodedDoctype . '/' . $encodedName);

        return $this->parseResponse($response->getStatusCode(), (string) $response->getBody());
    }

    /**
     * @param string $method e.g. "frappe.client.submit"
     * @param array $params
     * @return array
     */
    public function callMethod($method, array $params = [])
    {
        $response = $this->client->post('/api/method/' . $method, [
            'json' => $params,
        ]);

        return $this->parseResponse($response->getStatusCode(), (string) $response->getBody());
    }

    /**
     * @param int $statusCode
     * @param string $body
     * @return array
     */
    protected function parseResponse($statusCode, $body)
    {
        $decoded = json_decode($body, true);

        if ($statusCode >= 200 && $statusCode < 300) {
            return is_array($decoded) ? $decoded : [];
        }

        throw new Exception($this->extractErrorMessage($decoded, $body));
    }

    /**
     * @param array|null $decoded
     * @param string $rawBody
     * @return string
     */
    protected function extractErrorMessage($decoded, $rawBody)
    {
        if (! is_array($decoded)) {
            return 'ERP API request failed.';
        }

        if (! empty($decoded['exc'])) {
            if (preg_match('/:\s*(.+)$/m', $decoded['exc'], $matches)) {
                return trim($matches[1]);
            }
        }

        if (! empty($decoded['_server_messages'])) {
            $messages = json_decode($decoded['_server_messages'], true);
            if (is_array($messages)) {
                $texts = [];
                foreach ($messages as $message) {
                    $parsed = is_string($message) ? json_decode($message, true) : $message;
                    if (is_array($parsed) && isset($parsed['message'])) {
                        $texts[] = strip_tags($parsed['message']);
                    }
                }
                if (count($texts) > 0) {
                    return implode(' ', $texts);
                }
            }
        }

        if (! empty($decoded['errors']) && is_array($decoded['errors'])) {
            $errorTexts = [];
            foreach ($decoded['errors'] as $error) {
                if (is_array($error) && isset($error['message'])) {
                    $errorTexts[] = strip_tags($error['message']);
                } elseif (is_string($error)) {
                    $errorTexts[] = strip_tags($error);
                }
            }
            if (count($errorTexts) > 0) {
                return implode(' ', $errorTexts);
            }
        }

        if (! empty($decoded['message'])) {
            if (is_string($decoded['message'])) {
                return $decoded['message'];
            }
            if (is_array($decoded['message']) && isset($decoded['message']['message'])) {
                return strip_tags($decoded['message']['message']);
            }
        }

        return 'ERP API request failed.';
    }
}

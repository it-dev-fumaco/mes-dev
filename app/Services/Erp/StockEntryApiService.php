<?php

namespace App\Services\Erp;

use Exception;

/**
 * Stock Entry create/submit via ERPNext 15 REST API.
 */
class StockEntryApiService
{
    /** @var ErpNextClient */
    protected $erpClient;

    public function __construct(ErpNextClient $erpClient)
    {
        $this->erpClient = $erpClient;
    }

    /**
     * Create a Stock Entry document via ERPNext REST API.
     *
     * @param array $payload Stock Entry fields including child "items"
     * @return string created document name (e.g. STE-00001)
     */
    public function create(array $payload)
    {
        $response = $this->erpClient->createResource('Stock Entry', $payload);

        $name = isset($response['data']['name']) ? $response['data']['name'] : null;

        if (! $name) {
            throw new Exception('ERP did not return a Stock Entry name.');
        }

        return $name;
    }

    /**
     * Submit a Stock Entry (sets docstatus to 1 in ERPNext 15).
     *
     * @param string $stockEntryName
     * @return array submitted document from API message payload when present
     */
    public function submit($stockEntryName)
    {
        $response = $this->erpClient->callMethod('frappe.client.submit', [
            'doc' => [
                'doctype' => 'Stock Entry',
                'name' => $stockEntryName,
            ],
        ]);

        if (isset($response['message']) && is_array($response['message'])) {
            return $response['message'];
        }

        return $response;
    }

    /**
     * @param string $stockEntryName
     * @return array
     */
    public function get($stockEntryName)
    {
        $response = $this->erpClient->getResource('Stock Entry', $stockEntryName);

        return isset($response['data']) ? $response['data'] : $response;
    }

    /**
     * Create and optionally submit a manufacture stock entry.
     *
     * @param array $payload
     * @param int $docstatus 0 = draft only, 1 = create then submit
     * @return string
     */
    public function createManufactureEntry(array $payload, $docstatus)
    {
        $name = $this->create($payload);

        if ((int) $docstatus === 1) {
            $this->submit($name);
        }

        return $name;
    }
}

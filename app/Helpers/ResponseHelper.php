<?php

namespace App\Helpers;

use Illuminate\Http\Client\Response;

class ResponseHelper
{
    /**
     * Extract data from API response
     *
     * @param Response $response
     * @param string|null $key Optional key to extract specific data
     * @return array
     */
    public static function getData(Response $response, ?string $key = null): array
    {
        $body = $response->json();
        
        if ($body === null) {
            return [];
        }
        
        // If a specific key is requested
        if ($key !== null) {
            return $body[$key] ?? [];
        }
        
        // Handle different response structures
        if (isset($body['data'])) {
            return $body['data'];
        }
        
        // Check for common resource keys
        $resourceKeys = ['authors', 'books', 'categories', 'users'];
        foreach ($resourceKeys as $resourceKey) {
            if (isset($body[$resourceKey])) {
                return $body[$resourceKey];
            }
        }
        
        return $body;
    }
}

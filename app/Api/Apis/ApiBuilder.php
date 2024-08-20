<?php

namespace MythicalSystemsFramework\Api\Apis;

interface ApiBuilder
{
    /**
     * This function should handle the request and return the response.
     */
    public function handleRequest(): void;
}

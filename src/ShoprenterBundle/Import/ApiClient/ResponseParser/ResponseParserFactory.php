<?php

namespace ShoprenterBundle\Import\ApiClient\ResponseParser;

use ShoprenterBundle\Import\ApiClient\ResponseParser\ResponseParser;
use ShoprenterBundle\Import\ApiClient\ResponseParser\XmlResponseParser;
use ShoprenterBundle\Import\ApiClient\ResponseParser\JsonResponseParser;
/**
 * ResponseParser Factory
 *
 * @author Kántor András
 * @since 2013.02.22. 14:56
 */
class ResponseParserFactory
{
    /**
     * @param $contentType
     * @return XmlResponseParser
     */
    public function createParser($contentType)
    {
        switch ($contentType) {
            case 'application/xml':
                return new XmlResponseParser();
            case 'application/json':
                return new JsonResponseParser();
        }

        return false;
    }
}

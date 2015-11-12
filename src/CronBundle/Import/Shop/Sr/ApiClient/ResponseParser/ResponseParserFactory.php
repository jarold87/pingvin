<?php

namespace CronBundle\Import\Shop\Sr\ApiClient\ResponseParser;

use CronBundle\Import\Shop\Sr\ApiClient\ResponseParser\ResponseParser;
use CronBundle\Import\Shop\Sr\ApiClient\ResponseParser\XmlResponseParser;
use CronBundle\Import\Shop\Sr\ApiClient\ResponseParser\JsonResponseParser;
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

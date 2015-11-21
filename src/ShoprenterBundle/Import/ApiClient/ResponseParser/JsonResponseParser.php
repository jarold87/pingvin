<?php

namespace ShoprenterBundle\Import\ApiClient\ResponseParser;

/**
 * Json Response parser
 *
 * @author Kántor András
 * @since 2013.02.22. 14:32
 */
class JsonResponseParser extends ResponseParser
{
    /**
     * @return array
     */
    public function parse()
    {
        return json_decode($this->response, true);
    }
}

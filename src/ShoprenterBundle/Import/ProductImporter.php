<?php

namespace ShoprenterBundle\Import;

use CronBundle\Import\ProductImporter as MainProductImporter;
use CronBundle\Import\ImporterInterface;
use ShoprenterBundle\Import\Component\RequestModel\ProductRequestModel;
use ShoprenterBundle\Import\Component\ClientAdapter\ClientAdapter;

class ProductImporter extends MainProductImporter implements ImporterInterface
{
    /** @var string */
    protected $outerIdKey = 'product_id';

    /** @var ClientAdapter */
    protected $clientAdapter;

    /** @var ProductRequestModel */
    protected $requestModel;

    public function import()
    {
        if ($this->getError()) {
            $this->saveImportLog();
            return;
        }
        $this->loadLanguageId();
        parent::import();
    }

    protected function loadLanguageId()
    {
        $request = $this->requestModel->getLanguageRequest();
        $data = $this->clientAdapter->getRequest($request);
        if (!isset($data['language_id'])) {
            $this->addError($this->importName . ' -> not isset language_id');
        }
        $languageOuterId = $data['language_id'];
        $this->requestModel->setLanguageOuterId($languageOuterId);
    }
}
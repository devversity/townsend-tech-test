<?php

namespace App\Refactor;

use App\Refactor\Abstractions\SectionProducts;
use App\store_products;

class SectionProductsV1 extends SectionProducts
{
    /**
     * Sort (default is expected to be 0)
     *
     * @var string
     */
    public string $sort = '0';

    /**
     * Version 1
     *
     * @return array
     */
    public function get()
    {
        return (new store_products)->sectionProducts($this->storeId, $this->sectionName, $this->limit, $this->page, $this->sort);
    }
}

<?php

namespace App\Refactor\Abstractions;

use App\Models\Section;
use App\Models\StoreProduct;
use App\Refactor\Interfaces\Gettable;
use App\store_products;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

/**
 * Stops code duplication throughout the concrete versioned SectionProduct sub classes.
 */
abstract class SectionProducts implements Gettable
{
    /**
     * Store Id
     *
     * @var int
     */
    protected int $storeId = 3;

    /**
     * Limit
     *
     * @var int
     */
    protected int $limit = 8;

    /**
     * Offset
     *
     * @var int
     */
    protected int $offset = 0;

    /**
     * Sort
     *
     * @var string
     */
    protected string $sort = '';

    /**
     * Page
     *
     * @var int
     */
    protected int $page = 1;

    /**
     * Section Id
     *
     * @var int
     */
    protected int $sectionId = 0;

    /**
     * Section Name
     *
     * @var string
     */
    protected string $sectionName = '';

    /**
     * Currency
     *
     * @var string
     */
    protected string $currency = '';

    /**
     * Preview Mode
     *
     * @var bool
     */
    protected bool $previewMode = false;


    /**
     * Images Domain
     *
     * @var string
     */
    protected string $imagesDomain = "https://img.tmstor.es/";

    /**
     * Select query
     *
     * @var array|string[]
     */
    protected array $select = [
        'id',
        'position',
        'artist_id',
        'type',
        'display_name',
        'name',
        'launch_date',
        'remove_date',
        'description',
        'available',
        'price',
        'euro_price',
        'dollar_price',
        'image_format',
        'disabled_countries',
        'release_date',
        'available'
    ];

    /**
     * Sets the Store ID
     *
     * @param int $storeId
     */
    public function __construct(int $storeId)
    {
        $this->storeId = $storeId;

        return $this;
    }

    /**
     * IMO - needs to be moved into its own class.
     *
     * @return string[]
     */
    public function getGeocode()
    {
        //Return GB default for the purpose of the test
        return ['country' => 'GB'];
    }

    /**
     * Determine the sort order
     *
     * @return Collection
     */
    protected function applySort(Collection $storeProducts)
    {
        switch ($this->sort) {
            case "az":
                return $storeProducts->sortBy('title', SORT_REGULAR, true);
                break;
            case "za":
                return $storeProducts->sortBy('title', SORT_REGULAR, false);
                break;
            case "low":
                return $storeProducts->sortBy('price', SORT_REGULAR, true);
                break;
            case "high":
                return $storeProducts->sortBy('price', SORT_REGULAR, false);
                break;
            case "old":
                return $storeProducts->sortBy('release_date', SORT_REGULAR, false);
                break;
            case "new":
                return $storeProducts->sortBy('release_date', SORT_REGULAR, true);
                break;
        }

        return $storeProducts
            ->sortBy('position', SORT_REGULAR, true)
            ->sortBy('release_date', SORT_REGULAR, true);
    }

    /**
     * Set the section name
     *
     * @param string $sectionName
     *
     * @return $this
     */
    public function section(string $sectionName)
    {
        $this->sectionName = $sectionName;

        if (!empty($this->sectionName)) {
            $this->sectionId = Section::where('description', $sectionName)
                ->pluck('id')
                ->firstOrFail();
        }

        return $this;
    }

    /**
     * Set the limit
     *
     * @param int $limit
     *
     * @return $this
     */
    public function limit(int $limit)
    {
        $this->limit = max(1, $limit);
        $this->offset = ($this->page-1) * $this->limit;

        return $this;
    }

    /**
     * Set the page
     *
     * @param int $page
     *
     * @return $this
     */
    public function page(int $page)
    {
        $this->page = max(1, $page);

        return $this;
    }

    /**
     * Set the preview Mode
     *
     * @param bool $previewMode
     *
     * @return $this
     */
    public function previewMode(bool $previewMode)
    {
        $this->previewMode = $previewMode;

        return $this;
    }

    /**
     * Set the currency
     *
     * @param string $currency
     *
     * @return $this
     */
    public function currency(string $currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Set the sort
     *
     * @param string $sort
     *
     * @return $this
     */
    public function sort(string $sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Return results
     *
     * @return array
     */
    abstract function get();
}

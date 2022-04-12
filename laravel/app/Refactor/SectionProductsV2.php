<?php

namespace App\Refactor;

use App\Models\StoreProduct;
use App\Refactor\Abstractions\SectionProducts;
use Illuminate\Support\Carbon;

class SectionProductsV2 extends SectionProducts
{
    /**
     * Version 2
     *
     * @return array
     */
    public function get()
    {
       $storeProducts = StoreProduct::with(['sections', 'artist'])
           ->where('store_id', $this->storeId)
           ->where('deleted', 0)
           ->where('available', 1);

       if (!empty($this->sectionId)) {
           $storeProducts = $storeProducts
               ->whereRelation('sections', 'section_id', $this->sectionId);
       }

       $storeProducts = $storeProducts->get($this->select);
       $totalCount = $storeProducts->count();
       $storeProducts = $storeProducts->skip($this->offset)->take($this->limit);
       $noPages = ceil($totalCount / $this->limit);

       $storeProducts = $storeProducts->map(function ($storeProduct) {
           if (strlen($storeProduct->image_format) > 2) {
               $image = $this->imagesDomain . $storeProduct->id . "." . $storeProduct->image_format;
           } else {
               $image = $this->imagesDomain . "noimage.jpg";
           }

           // Checks disabled countries, which changes its availability
           if (!empty($storeProduct->disabled_countries)) {
               $countries = explode(',', $storeProduct->disabled_countries);
               $geocode = $this->getGeocode();
               $countryCode = $geocode['country'];
               $storeProduct->available = (int)!(is_array($countries) && in_array($countryCode, $countries));
           }

           // Checks if has a removal date and that date is in the past
           if (
               $storeProduct->remove_date !== "0000-00-00 00:00:00" &&
               Carbon::parse($storeProduct->remove_date)->isPast()
           ) {
               $storeProduct->available = 0;
           }

           // Determine which price displays
           $price = $storeProduct->price;
           switch ($this->currency) {
               case "USD":
                   $price = $storeProduct->dollar_price;
                   break;
               case "EUR":
                   $price = $storeProduct->euro_price;
                   break;
           }

           return [
               'image' => $image,
               'id' => $storeProduct->id,
               'position' => !empty($this->sectionId) ? $storeProduct->sections[0]->pivot->position : $storeProduct->position,
               'artist' => $storeProduct->artist->name,
               'title' => strlen($storeProduct->display_name) > 3 ? $storeProduct->display_name : $storeProduct->name,
               'description' => $storeProduct->description,
               'price' => $price,
               'format' => $storeProduct->type,
               'release_date' => $storeProduct->release_date,
               'launch_date' => $storeProduct->launch_date,
               'available' => $storeProduct->available
           ];

       })->filter(function ($storeProduct) {
           // If its available AND has a launch date AND its not in preview mode AND the launch date is the in future
           if (
               $storeProduct['available'] === 1 &&
               $storeProduct['launch_date'] !== "0000-00-00 00:00:00" &&
               $this->previewMode === false &&
               Carbon::parse($storeProduct['launch_date'])->isFuture()
           ) {
               $storeProduct['available'] = 0;
           }

           return $storeProduct['available'] === 1;
       });

       $storeProducts = $this->applySort($storeProducts)->map(function ($storeProduct) {
           unset($storeProduct['position']);
           unset($storeProduct['available']);
           unset($storeProduct['launch_date']); // Remove additional fields so we match V1.
           return $storeProduct;
       })->toArray();

       $storeProducts['pages'] = $noPages;

       // Reversed to match original method
       return array_reverse($storeProducts);
   }
}

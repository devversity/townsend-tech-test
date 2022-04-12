<?php

namespace App\Http\Controllers;

use App\Refactor\Factories\VersionManager;
use Illuminate\Http\Request;

class ProductsController extends Controller
{

    public function __construct()
    {
        $this->storeId = 3;
    }

    /**
     * Refactored sectionProducts
     *
     * @param string $sectionName
     */
    public function sectionProducts(Request $request, string $sectionName = '')
    {
        // Adjustment of current page, sort and limit is possible via GET parameters
        // i.e ?page=1, ?limit=8, ?sort=az
        $version1 = VersionManager::SectionProducts($this->storeId, 1)
            ->section($sectionName)
            ->page($request->input('page', 1))
            ->limit($request->input('limit', 8))
            ->sort($request->input('sort', 'az'))
            ->get();

        // Adjustment of current page, sort and limit is possible via GET parameters
        // i.e ?page=1, ?limit=8, ?sort=az
        $version2 = VersionManager::SectionProducts($this->storeId, 2)
            ->section($sectionName)
            ->page($request->input('page', 1))
            ->limit($request->input('limit', 8))
            ->sort($request->input('sort', 'az'))
            ->get();

        // You'd ideally create a unit test here which assets true if they both return the same
        // Based on given parameters. I've tested this below, accessible via debug GET param '?debug=Y'
        if ($request->input('debug')) {
            dd($version1, $version2);
        }

        return response()->json($version2);
    }

}

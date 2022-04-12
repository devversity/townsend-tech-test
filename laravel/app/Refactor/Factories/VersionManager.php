<?php

namespace App\Refactor\Factories;

use App\Refactor\SectionProductsV1;
use App\Refactor\SectionProductsV2;

/**
 * Determines which version of a given class is required.
 */
class VersionManager
{
    /**
     * Static method which returns the correct SectionProducts version
     * Typically you'd have the version number set in the ENV file but for the purpose of this demo
     * I've added it as a parameter.
     *
     * You'd also perhaps consider using a config to map the version numbers to their respective classes.
     *
     * @param int $storeId
     * @param int $version
     */
    public static function SectionProducts(int $storeId, int $version)
    {
        if ($version === 1) {
            return new SectionProductsV1($storeId);
        } elseif ($version === 2) {
            return new SectionProductsV2($storeId);
        }

        // Throw an exception if we hit this point.
    }
}

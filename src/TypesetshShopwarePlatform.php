<?php

declare(strict_types=1);

namespace Typesetsh\ShopwarePlatform;

use Shopware\Core\Framework\Bundle;
use Shopware\Core\Framework\Parameter\AdditionalBundleParameters;
use Shopware\Core\Framework\Plugin;
use Typesetsh;

class TypesetshShopwarePlatform extends Plugin
{
    /**
     * @return Bundle[]
     */
    public function getAdditionalBundles(AdditionalBundleParameters $parameters): array
    {
        return [
            new Typesetsh\PdfBundle\TypesetshPdfBundle()
        ];
    }
}

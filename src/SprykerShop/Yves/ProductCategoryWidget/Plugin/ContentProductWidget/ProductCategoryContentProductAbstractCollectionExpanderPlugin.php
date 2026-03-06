<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerShop\Yves\ProductCategoryWidget\Plugin\ContentProductWidget;

use Spryker\Yves\Kernel\AbstractPlugin;
use SprykerShop\Yves\ContentProductWidget\Dependency\Plugin\ContentProductAbstractCollectionExpanderPluginInterface;

/**
 * @method \SprykerShop\Yves\ProductCategoryWidget\ProductCategoryWidgetFactory getFactory()
 *
 * Expands content product abstract collection with main category per product.
 */
class ProductCategoryContentProductAbstractCollectionExpanderPlugin extends AbstractPlugin implements ContentProductAbstractCollectionExpanderPluginInterface
{
    /**
     * Specification:
     * - Expands the product abstract view collection with main category per product.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\ProductViewTransfer> $productViewTransferCollection
     *
     * @return array<\Generated\Shared\Transfer\ProductViewTransfer>
     */
    public function expand(array $productViewTransferCollection): array
    {
        return $this->getFactory()
            ->createContentProductAbstractCategoryExpander()
            ->expand($productViewTransferCollection, $this->getLocale());
    }
}

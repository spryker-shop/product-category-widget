<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerShop\Yves\ProductCategoryWidget\Expander;

interface ContentProductAbstractCategoryExpanderInterface
{
    /**
     * @param array<\Generated\Shared\Transfer\ProductViewTransfer> $productViewTransferCollection
     * @param string $localeName
     *
     * @return array<\Generated\Shared\Transfer\ProductViewTransfer>
     */
    public function expand(array $productViewTransferCollection, string $localeName): array;
}

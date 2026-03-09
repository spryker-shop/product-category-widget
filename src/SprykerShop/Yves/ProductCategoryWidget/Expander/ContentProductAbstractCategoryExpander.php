<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerShop\Yves\ProductCategoryWidget\Expander;

use ArrayObject;
use Generated\Shared\Transfer\CategoryTransfer;
use Generated\Shared\Transfer\ProductCategoryStorageTransfer;
use SprykerShop\Yves\ProductCategoryWidget\Dependency\Client\ProductCategoryWidgetToProductCategoryStorageClientInterface;
use SprykerShop\Yves\ProductCategoryWidget\Dependency\Client\ProductCategoryWidgetToStoreClientInterface;

class ContentProductAbstractCategoryExpander implements ContentProductAbstractCategoryExpanderInterface
{
    public function __construct(
        protected ProductCategoryWidgetToProductCategoryStorageClientInterface $productCategoryStorageClient,
        protected ProductCategoryWidgetToStoreClientInterface $storeClient
    ) {
    }

    /**
     * @param array<\Generated\Shared\Transfer\ProductViewTransfer> $productViewTransferCollection
     * @param string $localeName
     *
     * @return array<\Generated\Shared\Transfer\ProductViewTransfer>
     */
    public function expand(array $productViewTransferCollection, string $localeName): array
    {
        $idProductAbstracts = $this->collectIdProductAbstracts($productViewTransferCollection);

        if ($idProductAbstracts === []) {
            return $productViewTransferCollection;
        }

        $productAbstractCategoryStorageTransferCollection = $this->productCategoryStorageClient
            ->findBulkProductAbstractCategory(
                $idProductAbstracts,
                $localeName,
                $this->storeClient->getCurrentStore()->getNameOrFail(),
            );

        $productCategoriesStorageTransferByIdProductAbstract = $this->getProductCategoryStorageTransfersIndexedByIdProductAbstract(
            $productAbstractCategoryStorageTransferCollection,
        );

        foreach ($productViewTransferCollection as $productViewTransfer) {
            $idProductAbstract = $productViewTransfer->getIdProductAbstractOrFail();

            if (!isset($productCategoriesStorageTransferByIdProductAbstract[$idProductAbstract])) {
                continue;
            }

            $productCategoryStorageTransfers = $productCategoriesStorageTransferByIdProductAbstract[$idProductAbstract];

            $categoryTransfers = array_map(
                fn (ProductCategoryStorageTransfer $productCategoryStorageTransfer) => (new CategoryTransfer())->setName($productCategoryStorageTransfer->getNameOrFail()),
                $productCategoryStorageTransfers,
            );

            $productViewTransfer->setCategories(new ArrayObject($categoryTransfers));
        }

        return $productViewTransferCollection;
    }

    /**
     * @param array<\Generated\Shared\Transfer\ProductViewTransfer> $productViewTransferCollection
     *
     * @return list<int>
     */
    protected function collectIdProductAbstracts(array $productViewTransferCollection): array
    {
        $idProductAbstracts = [];

        foreach ($productViewTransferCollection as $productViewTransfer) {
            $idProductAbstracts[] = $productViewTransfer->getIdProductAbstractOrFail();
        }

        return $idProductAbstracts;
    }

    /**
     * @param array<\Generated\Shared\Transfer\ProductAbstractCategoryStorageTransfer> $productAbstractCategoryStorageTransferCollection
     *
     * @return array<int, array<\Generated\Shared\Transfer\ProductCategoryStorageTransfer>>
     */
    protected function getProductCategoryStorageTransfersIndexedByIdProductAbstract(array $productAbstractCategoryStorageTransferCollection): array
    {
        $productCategoryStorageTransferByIdProductAbstract = [];

        foreach ($productAbstractCategoryStorageTransferCollection as $productAbstractCategoryStorageTransfer) {
            $productCategoryStorageTransferCollection = $productAbstractCategoryStorageTransfer->getCategories();

            if ($productCategoryStorageTransferCollection->count() === 0) {
                continue;
            }

            $productCategoryStorageTransferList = $productCategoryStorageTransferCollection->getArrayCopy();
            $sortedProductCategoryStorageTransferList = $this->productCategoryStorageClient
                ->sortProductCategories($productCategoryStorageTransferList);

            $productCategoryStorageTransferByIdProductAbstract[$productAbstractCategoryStorageTransfer->getIdProductAbstractOrFail()] = $sortedProductCategoryStorageTransferList;
        }

        return $productCategoryStorageTransferByIdProductAbstract;
    }
}

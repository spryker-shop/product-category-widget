<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerShopTest\Yves\ProductCategoryWidget\Expander;

use ArrayObject;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\ProductAbstractCategoryStorageTransfer;
use Generated\Shared\Transfer\ProductCategoryStorageTransfer;
use Generated\Shared\Transfer\ProductViewTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use SprykerShop\Yves\ProductCategoryWidget\Dependency\Client\ProductCategoryWidgetToProductCategoryStorageClientInterface;
use SprykerShop\Yves\ProductCategoryWidget\Dependency\Client\ProductCategoryWidgetToStoreClientInterface;
use SprykerShop\Yves\ProductCategoryWidget\Expander\ContentProductAbstractCategoryExpander;

/**
 * Auto-generated group annotations
 *
 * @group SprykerShop
 * @group Yves
 * @group Presentation
 * @group ProductCategoryWidget
 * @group Expander
 * @group ContentProductAbstractCategoryExpanderTest
 * Add your own group annotations below this line
 */
class ContentProductAbstractCategoryExpanderTest extends Unit
{
    /**
     * @return void
     */
    public function testExpandReturnsSameCollectionWhenNoProductAbstracts(): void
    {
        // Arrange
        $productCategoryStorageClientMock = $this->createMock(ProductCategoryWidgetToProductCategoryStorageClientInterface::class);
        $storeClientMock = $this->createMock(ProductCategoryWidgetToStoreClientInterface::class);

        $productCategoryStorageClientMock->expects($this->never())
            ->method('findBulkProductAbstractCategory');

        $expander = new ContentProductAbstractCategoryExpander(
            $productCategoryStorageClientMock,
            $storeClientMock,
        );

        $productViewTransferCollection = [];

        // Act
        $result = $expander->expand($productViewTransferCollection, 'en_US');

        // Assert
        $this->assertSame($productViewTransferCollection, $result);
    }

    /**
     * @return void
     */
    public function testExpandAddsCategoriesToProductViewTransfer(): void
    {
        // Arrange
        $productCategoryStorageClientMock = $this->createMock(ProductCategoryWidgetToProductCategoryStorageClientInterface::class);
        $storeClientMock = $this->createMock(ProductCategoryWidgetToStoreClientInterface::class);

        $idProductAbstract = 1;
        $localeName = 'en_US';
        $storeName = 'DE';
        $mainCategoryName = 'Main category';
        $secondaryCategoryName = 'Secondary category';

        $productViewTransfer = (new ProductViewTransfer())
            ->setIdProductAbstract($idProductAbstract);

        $productViewTransferCollection = [$productViewTransfer];

        $storeClientMock->expects($this->once())
            ->method('getCurrentStore')
            ->willReturn((new StoreTransfer())->setName($storeName));

        $mainCategoryStorageTransfer = (new ProductCategoryStorageTransfer())
            ->setName($mainCategoryName);

        $secondaryCategoryStorageTransfer = (new ProductCategoryStorageTransfer())
            ->setName($secondaryCategoryName);

        $productAbstractCategoryStorageTransfer = (new ProductAbstractCategoryStorageTransfer())
            ->setIdProductAbstract($idProductAbstract)
            ->setCategories(new ArrayObject([$secondaryCategoryStorageTransfer, $mainCategoryStorageTransfer]));

        $productCategoryStorageClientMock->expects($this->once())
            ->method('findBulkProductAbstractCategory')
            ->with([$idProductAbstract], $localeName, $storeName)
            ->willReturn([$productAbstractCategoryStorageTransfer]);

        $productCategoryStorageClientMock->expects($this->once())
            ->method('sortProductCategories')
            ->with([$secondaryCategoryStorageTransfer, $mainCategoryStorageTransfer])
            ->willReturn([$mainCategoryStorageTransfer, $secondaryCategoryStorageTransfer]);

        $expander = new ContentProductAbstractCategoryExpander(
            $productCategoryStorageClientMock,
            $storeClientMock,
        );

        // Act
        $result = $expander->expand($productViewTransferCollection, $localeName);

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(1, $result);

        /** @var \Generated\Shared\Transfer\ProductViewTransfer $resultProductViewTransfer */
        $resultProductViewTransfer = $result[0];

        $categoryTransferCollection = $resultProductViewTransfer->getCategories();
        $this->assertNotNull($categoryTransferCollection);
        $this->assertSame(2, $categoryTransferCollection->count());

        $categoryNames = [];
        foreach ($categoryTransferCollection as $categoryTransfer) {
            $categoryNames[] = $categoryTransfer->getName();
        }

        $this->assertSame([$mainCategoryName, $secondaryCategoryName], $categoryNames);
    }
}

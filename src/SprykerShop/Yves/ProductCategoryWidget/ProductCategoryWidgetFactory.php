<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\ProductCategoryWidget;

use Spryker\Yves\Kernel\AbstractFactory;
use SprykerShop\Yves\ProductCategoryWidget\Dependency\Client\ProductCategoryWidgetToProductCategoryStorageClientInterface;
use SprykerShop\Yves\ProductCategoryWidget\Dependency\Client\ProductCategoryWidgetToStoreClientInterface;
use SprykerShop\Yves\ProductCategoryWidget\Expander\ContentProductAbstractCategoryExpander;
use SprykerShop\Yves\ProductCategoryWidget\Expander\ContentProductAbstractCategoryExpanderInterface;

class ProductCategoryWidgetFactory extends AbstractFactory
{
    public function getProductCategoryStorageClient(): ProductCategoryWidgetToProductCategoryStorageClientInterface
    {
        return $this->getProvidedDependency(ProductCategoryWidgetDependencyProvider::CLIENT_PRODUCT_CATEGORY_STORAGE);
    }

    public function getStoreClient(): ProductCategoryWidgetToStoreClientInterface
    {
        return $this->getProvidedDependency(ProductCategoryWidgetDependencyProvider::CLIENT_STORE);
    }

    public function createContentProductAbstractCategoryExpander(): ContentProductAbstractCategoryExpanderInterface
    {
        return new ContentProductAbstractCategoryExpander(
            $this->getProductCategoryStorageClient(),
            $this->getStoreClient(),
        );
    }
}

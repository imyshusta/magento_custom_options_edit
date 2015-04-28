<?php

/**
 * Class Session_CustomOptions_Helper_Data
 */
class Session_CustomOptions_Helper_Data extends Mage_Core_Helper_Abstract
{
    /** @var null  */
    protected $currentOrderItemsProducts = null;

    /**
     * @param $orderItem
     * @return bool
     */
    public function hasOrderItemCustomOptions($orderItem)
    {
        $itemProduct = $this->getCurrentOrderItemsProducts($orderItem)->getItemById($orderItem->getProductId());
        return $itemProduct && $itemProduct->getOptions();
    }

    /**
     * @param $orderItem
     * @return Varien_Object
     */
    public function getOrderItemProduct($orderItem)
    {
        return $this->getCurrentOrderItemsProducts($orderItem)->getItemById($orderItem->getProductId());
    }

    /**
     * @param $orderItem
     * @return Mage_Catalog_Model_Resource_Product_Collection|null
     */
    public function getCurrentOrderItemsProducts($orderItem)
    {
        if (is_null($this->currentOrderItemsProducts)) {
            $items = $orderItem->getOrder()->getItemsCollection();
            $productIds = array();

            foreach ($items as $item) {
                $productIds[] = $item->getProductId();
            }

            $productCollection = Mage::getModel('catalog/product')->getCollection();
            $productCollection->addFieldToFilter('entity_id', array('in' => $productIds));

            foreach ($productCollection as $product) {
                if ($product->getHasOptions()) {
                    foreach ($product->getProductOptionsCollection() as $option) {
                        $option->setProduct($product);
                        $product->addOption($option);
                    }
                }
            }

            $this->currentOrderItemsProducts = $productCollection;
        }

        return $this->currentOrderItemsProducts;
    }
}
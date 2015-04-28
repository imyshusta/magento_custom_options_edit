<?php

/**
 * Class Session_CustomOptions_Model_Observer
 */
class Session_CustomOptions_Model_Observer extends Varien_Event_Observer
{
    /**
     * @param Varien_Event_Observer $observer
     */
    public function addEditOptionsLink(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Sales_Items_Column_Name) {
            $item = $block->getItem();
            $helper = Mage::helper('session_customoptions');
            if ($helper->hasOrderItemCustomOptions($item)) {
                $html = $observer->getTransport()->getHtml();
                $productHelper = Mage::helper('catalog/product');
                $itemProduct = $helper->getOrderItemProduct($item);
                $productHelper->prepareProductOptions($itemProduct, $item->getBuyRequest());

                $content = json_encode($block->
                    getLayout()->getBlock('session.customoptions.adminhtml.edit')
                    ->setProduct($itemProduct)
                    ->setOrderItem($item)
                    ->toHtml());
                $html .= '<script type="text/javascript">edit_custom_options_content=' . $content . ';</script>';
                $html .= '<a href="javascript:editCustomOptions(' . $item->getId() . ')">'
                    . $block->__('Edit Custom Options')
                    . '</a>';

                $observer->getTransport()->setHtml($html);
            }
        }
    }
}

<?php

/**
 * Class Session_CustomOptions_Adminhtml_OptionsController
 */
class Session_CustomOptions_Adminhtml_OptionsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @throws Exception
     */
    public function editAction()
    {
        $orderItemId = intval($this->getRequest()->getParam('order_item_id'));
        if ($orderItemId > 0) {
            $orderItem = Mage::getModel('sales/order_item')->load($orderItemId);

            $options = $orderItem->getProductOptions();

            $product = $orderItem->getProduct();

            $requestOptions = $this->getRequest()->getParam('options', array());
            $buyRequest = $orderItem->getBuyRequest();
            $newOptions = array_replace($buyRequest->getOptions(), $requestOptions);
            $buyRequest->setOptions($newOptions);
            $product->getTypeInstance(true)->prepareForCartAdvanced($buyRequest, $product);
            $optionArr['options'] = array();

            if ($optionIds = $product->getCustomOption('option_ids')) {
                foreach (explode(',', $optionIds->getValue()) as $optionId) {
                    if ($option = $product->getOptionById($optionId)) {

                        $confItemOption = $product->getCustomOption(Mage_Catalog_Model_Product_Type_Abstract::OPTION_PREFIX . $option->getId());

                        $group = $option->groupFactory($option->getType())
                            ->setOption($option)
                            ->setProduct($product)
                            ->setConfigurationItemOption($confItemOption);

                        $optionArr['options'][] = array(
                            'label' => $option->getTitle(),
                            'value' => $group->getFormattedOptionValue($confItemOption->getValue()),
                            'print_value' => $group->getPrintableOptionValue($confItemOption->getValue()),
                            'option_id' => $option->getId(),
                            'option_type' => $option->getType(),
                            'option_value' => $confItemOption->getValue(),
                            'custom_view' => $group->isCustomizedView()
                        );
                    }
                }
            }

            $options['options'] = $optionArr['options'];
            $options['info_buyRequest'] = $buyRequest->getData();
            $orderItem->setProductOptions($options);
            $orderItem->save();

            $this->loadLayout();
            $block = $this->getLayout()->getBlock('order_items')->setOrder($orderItem->getOrder());
            $itemHtml = $block->getItemHtml($orderItem);
            $itemHtml .= $block->getItemExtraInfoHtml($orderItem);
        } else {
            $itemHtml = Mage::helper('session_customoptions')->__("Error updating options. Please, update page and try again later.");
        }

        $this->getResponse()
            ->setHeader('Content-Type', 'text/html')
            ->setBody($itemHtml);
    }
}

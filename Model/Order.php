<?php
/**
 * UnCancelOrder
 *
 * @package Genmato_UnCancelOrder
 * @author  Vladimir Kerkhoff <support@genmato.com>
 * @created 2015-11-25
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */
namespace Genmato\UnCancelOrder\Model;

use \Magento\Sales\Model\Order as SalesOrder;

class Order extends SalesOrder
{
    public function uncancel($comment = '', $graceful = true)
    {
        if ($this->isCanceled()) {
            $state = self::STATE_PROCESSING;
            $productStockQty = [];
            foreach ($this->getAllItems() as $item) {
                $productStockQty[$item->getProductId()] = $item->getQtyCanceled();
                $item->setQtyCanceled(0);
                $item->setTaxCanceled(0);
                $item->setDiscountTaxCompensationCanceled(0);
                $this->_eventManager->dispatch('sales_order_item_uncancel', ['item' => $item]);
            }
            $this->_eventManager->dispatch(
                'sales_order_uncancel_inventory',
                [
                    'order' => $this,
                    'product_qty' => $productStockQty
                ]
            );

            $this->setSubtotalCanceled(0);
            $this->setBaseSubtotalCanceled(0);

            $this->setTaxCanceled(0);
            $this->setBaseTaxCanceled(0);

            $this->setShippingCanceled(0);
            $this->setBaseShippingCanceled(0);

            $this->setDiscountCanceled(0);
            $this->setBaseDiscountCanceled(0);

            $this->setTotalCanceled(0);
            $this->setBaseTotalCanceled(0);

            $this->setState($state)
                ->setStatus($this->getConfig()->getStateDefaultStatus($state));
            if (!empty($comment)) {
                $this->addStatusHistoryComment($comment, false);
            }

            $this->_eventManager->dispatch('order_uncancel_after', ['order' => $this]);
        } elseif (!$graceful) {
            throw new \Magento\Framework\Exception\LocalizedException(__('We cannot un-cancel this order.'));
        }
        return $this;
    }


} 

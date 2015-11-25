<?php
/**
 * UnCancelOrder
 *
 * @package Genmato_UnCancelOrder
 * @author  Vladimir Kerkhoff <support@genmato.com>
 * @created 2015-11-25
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */
namespace Genmato\UnCancelOrder\Observer;

use \Magento\Framework\Event\ObserverInterface;
use \Magento\CatalogInventory\Api\StockManagementInterface;
use \Magento\CatalogInventory\Model\Indexer\Stock\Processor as StockProcessor;
use \Magento\Framework\Event\Observer as EventObserver;
use \Psr\Log\LoggerInterface;

class SubtractInventoryObserver implements ObserverInterface
{
    /**
     * @var StockManagementInterface
     */
    protected $stockManagement;

    /**
     * @var \Magento\CatalogInventory\Observer\ItemsForReindex
     */
    protected $itemsForReindex;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * SubtractInventoryObserver constructor.
     * @param StockManagementInterface $stockManagement
     * @param StockProcessor $stockIndexerProcessor
     * @param LoggerInterface $logger
     */
    public function __construct(
        StockManagementInterface $stockManagement,
        StockProcessor $stockIndexerProcessor,
        LoggerInterface $logger
    ) {
        $this->stockManagement = $stockManagement;
        $this->stockIndexerProcessor = $stockIndexerProcessor;
        $this->logger = $logger;
    }

    /**
     * Subtract items qtys from stock related with uncancel products.
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $productQty = $observer->getEvent()->getProductQty();

        if ($order->getInventoryProcessed()) {
            return $this;
        }

        /**
         * Remember items
         */
        $itemsForReindex = $this->stockManagement->registerProductsSale(
            $productQty,
            $order->getStore()->getWebsiteId()
        );
        $this->logger->debug(var_export($productQty, true));
        $productIds = [];
        foreach ($itemsForReindex as $item) {
            $item->save();
            $productIds[] = $item->getProductId();
        }
        $this->logger->debug(var_export($productIds, true));
        if (!empty($productIds)) {
            $this->stockIndexerProcessor->reindexList($productIds);
        }

        $order->setInventoryProcessed(true);
        return $this;
    }
}
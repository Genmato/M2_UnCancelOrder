<?php
/**
 * UnCancelOrder
 *
 * @package Genmato_UnCancelOrder
 * @author  Vladimir Kerkhoff <support@genmato.com>
 * @created 2015-11-25
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */
namespace Genmato\UnCancelOrder\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use \Magento\Backend\App\Action\Context;
use \Genmato\UnCancelOrder\Model\OrderFactory;

class Uncancel extends Action
{
    /**
     * Genmato Extended Order factory
     *
     * @var \Genmato\UnCancelOrder\Model\OrderFactory
     */
    protected $demoFactory;

    /**
     * Initialize Group Controller
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Genmato\UnCancelOrder\Model\OrderFactory $orderFactory
     */
    public function __construct(
        Context $context,
        OrderFactory $orderFactory
    ) {
        $this->orderFactory = $orderFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Genmato_UnCancelOrder::uncancel');
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('order_id');


        if ($id) {
            $order = $this->orderFactory->create()->load($id);
            try {
                // Uncancel order
                $order->uncancel();
                // Save order details
                $order->save();
                // display success message
                $this->messageManager->addSuccess(__('You un-canceled the order.'));
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
            }
        } else {
            // display error message
            $this->messageManager->addError(__('Unable to un-cancel the order!'));
        }
        // go to order view page
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('sales/order/view', ['order_id' => $id]);
    }
}
<?php
/**
 * UnCancelOrder
 *
 * @package Genmato_UnCancelOrder
 * @author  Vladimir Kerkhoff <support@genmato.com>
 * @created 2015-11-25
 * @copyright Copyright (c) 2015 Genmato BV, https://genmato.com.
 */
namespace Genmato\UnCancelOrder\Plugin\Block\Widget;

use Magento\Framework\UrlInterface;

class Context
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\App\Action\Context
     */
    protected $_context = null;

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @param \Magento\Framework\Registry $coreRegistry
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Action\Context $context,
        UrlInterface $urlBuilder

    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_context = $context;
        $this->_urlBuilder = $urlBuilder;
    }


    public function afterGetButtonList(
        \Magento\Backend\Block\Widget\Context $subject,
        \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
    )
    {
        $request = $this->_context->getRequest();

        if($request->getFullActionName() == 'sales_order_view'){
            $order = $this->getOrder();
            if ($order && $order->getState()=='canceled') {
                $message = __('Are you sure you want to un-cancel this order?');
                $buttonList->add(
                    'order_uncancel',
                    [
                        'label' => __('Un-Cancel'),
                        'onclick' => "confirmSetLocation('{$message}', '".$this->getUnCancelUrl()."')",
                    ]
                );
            }
        }

        return $buttonList;
    }

    public function getOrder()
    {
        return $this->_coreRegistry->registry('sales_order');
    }

    public function getUnCancelUrl()
    {
        return $this->_urlBuilder->getUrl('*/*/uncancel', ['order_id'=>$this->getOrder()->getId()]);
    }
} 

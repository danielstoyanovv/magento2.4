<?php

declare(strict_types=1);

namespace Stoyanov\Restaurant\Controller\Restaurant;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Result\Page;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Stoyanov\Restaurant\Api\RestaurantManagerInterface;
use Magento\Framework\Event\ManagerInterface;

class Delete extends Action
{
    /**
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param RestaurantManagerInterface $manager
     * @param ForwardFactory $forward
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        Context $context,
        private PageFactory $pageFactory,
        private RestaurantManagerInterface $manager,
        private ForwardFactory $forward,
        private ManagerInterface $eventManager
    ) {
        parent::__construct($context);
    }

    /**
     * Delete Restaurant Action
     *
     * @return Page
     */
    public function execute(): Page
    {
        if ($this->_request->isPost()) {
            $id = (int) $this->_request->getParams()["id"];
            $forwardResult = $this->forward->create();
            if ($this->manager->deleteRestaurant($id)) {
                $this->eventManager->dispatch(
                    'restaurant_deleted',
                    ['restaurant_id' => $id]
                );
                $forwardResult->forward('index');
            }
            $this->messageManager->addSuccess(__('The restaurant is deleted!'));
        }
        return $this->pageFactory->create();
    }
}

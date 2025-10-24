<?php
declare(strict_types=1);

namespace Stoyanov\Restaurant\Controller\Adminhtml\Restaurant;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Stoyanov\Restaurant\Api\AccountManagerInterface;

class SaveProfile extends Action implements HttpPostActionInterface
{

    public const string ADMIN_RESOURCE = 'Stoyanov_Restaurant::restaurant_user';

    /**
     * @param Context $context
     * @param AccountManagerInterface $accountManager
     */
    public function __construct(
        private Context $context,
        private AccountManagerInterface $accountManager
    ) {
        parent::__construct($context);
    }

    /**
     * Create a new Profile logic
     *
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $post = $this->getRequest()->getPost()->toArray();
        if ($this->accountManager->createProfile($post)) {
            $this->messageManager->addSuccess(__('The new API profile was created!'));
        }

        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $redirect->setPath('*/*/login');
    }
}

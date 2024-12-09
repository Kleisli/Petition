<?php
namespace Kleisli\Petition\Controller\Backend;

/*
 * This file is part of the Profolio.Data package.
 */

use Neos\ContentRepository\Domain\Service\ContextFactoryInterface;
use Neos\Eel\FlowQuery\FlowQuery;
use Neos\Flow\Annotations as Flow;
use Neos\Neos\Controller\Module\AbstractModuleController;

class PetitionController extends AbstractModuleController
{


    #[Flow\Inject]
    protected ContextFactoryInterface $contextFactory;

    /**
     * @return void
     */
    public function indexAction()
    {
        $context = $this->contextFactory->create();
        $q = new FlowQuery([$context->getCurrentSiteNode()]);

        $this->view->assign('petitions', $q->find('[instanceof Kleisli.Petition:Content.SignatureForm]')
            ->context(['invisibleContentShown' => true])
            ->closest('[instanceof Neos.Neos:Document]'));
    }

}

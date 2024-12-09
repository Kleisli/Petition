<?php
namespace Kleisli\Petition\Controller\Backend;

use Kleisli\Petition\Domain\Model\Signature;
use Milly\CrudUI\Controller\CrudControllerTrait;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\View\ViewInterface;
use Neos\Fusion\View\FusionView;
use Neos\Neos\Controller\Module\AbstractModuleController;

class SignatureController extends AbstractModuleController
{
    const ENTITY_CLASSNAME = Signature::class;
    use CrudControllerTrait;

    /**
     * @return void
     */
    public function indexAction(string $parent = '')
    {
        $this->view->assign('objects', $this->getRepository()->findByPetitionNodeIdentifier($parent)->toArray());
        $this->view->assign('numberOfVerified', $this->getRepository()->getNumberOfVerifiedByPetition($parent));

        $this->view->assign('parent', $parent);
        $this->view->assign('configuration', $this->getCrudUIConfiguration('index'));
        $this->view->assign('CrudUIModelClass', $this->getModelClass());
    }
}

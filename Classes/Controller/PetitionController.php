<?php
namespace Kleisli\Petition\Controller;

/*
 * This file is part of the Kleisli.Petition package.
 */

use Kleisli\Petition\Domain\Model\Signature;
use Kleisli\Petition\Domain\Repository\SignatureRepository;
use Neos\ContentRepository\Domain\Factory\NodeFactory;
use Neos\ContentRepository\Domain\Model\Node;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\ContentRepository\Domain\Repository\NodeDataRepository;
use Neos\ContentRepository\Domain\Service\ContextFactoryInterface;
use Neos\Eel\FlowQuery\FlowQuery;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\I18n\Locale;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Neos\Service\LinkingService;
use Sitegeist\Neos\SymfonyMailer\Factories\MailerFactory;
use Sitegeist\Neos\SymfonyMailer\Factories\MailFactory;
use WebSupply\RouteAnnotation\Annotations as Routing;

#[Routing\Route(path: 'petition')]
class PetitionController extends ActionController
{

    #[Flow\Inject]
    protected SignatureRepository $signatureRepository;

    #[Flow\Inject]
    protected LinkingService $linkingService;

    #[Flow\Inject]
    protected NodeDataRepository $nodeDataRepository;

    #[Flow\Inject]
    protected NodeFactory $nodeFactory;

    #[Flow\Inject]
    protected ContextFactoryInterface $contextFactory;

    #[Flow\Inject]
    protected MailerFactory $mailerFactory;

    #[Flow\Inject]
    protected MailFactory $mailFactory;

    /**
     * @param Signature $object
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\StopActionException
     * @throws \Neos\Flow\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \Neos\Flow\Persistence\Exception\IllegalObjectTypeException
     */
    public function signAction(Signature $object, string $language = 'de')
    {
        $this->signatureRepository->add($object);

        $dimensions = ['language' => [$language]];
        /** @var Node $petitionNode */
        $petitionNode = $this->findNodeByIdentifier($object->getPetitionNodeIdentifier(), $dimensions);
        $nodeUri = $this->linkingService->createNodeUri($this->controllerContext, $petitionNode);
        $q = new FlowQuery([$petitionNode]);
        $signatureForm = $q->find('[instanceof Kleisli.Petition:Content.SignatureForm]')->get(0);

        $arguments = ['token' => $object->getToken(), 'language' => $language];
        $mailText = str_replace(
            ['{firstName}', '{lastName}', '{email}', '{link}'],
            [$object->getFirstName(), $object->getLastName(), $object->getEmail(), $this->uriBuilder->setCreateAbsoluteUri(true)->uriFor('verify', $arguments)],
            $signatureForm->getProperty('mailText')
        );

        $mailer = $this->mailerFactory->createMailer();
        $mail = $this->mailFactory->createMail(
            $petitionNode->getLabel(),
            $object->getEmail(),
            'mailer@dergewerbeverein.ch',
            $mailText,
            null,
            'info@dergewerbeverein.ch',
        );
        $mailer->send($mail);

        $this->redirectToUri($nodeUri."?action=signed#petition");
    }

    /**
     * @param string $token
     * @return void
     * @throws \Neos\Flow\Mvc\Exception\StopActionException
     * @throws \Neos\Flow\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \Neos\Flow\Persistence\Exception\IllegalObjectTypeException
     */
    #[Routing\Route("verify/{token}")]
    public function verifyAction(string $token, string $language = 'de')
    {
        /** @var Signature $signature */
        $signature = $this->signatureRepository->findOneByToken($token);
        if($signature){
            $signature->setVerifiedAt(new \DateTime());
            $this->signatureRepository->update($signature);
        }
        $dimensions = ['language' => [$language]];
        /** @var Node $petitionNode */
        $petitionNode = $this->findNodeByIdentifier($signature->getPetitionNodeIdentifier(), $dimensions);
        $nodeUri = $this->linkingService->createNodeUri($this->controllerContext, $petitionNode);
        $this->persistenceManager->persistAll();
        $this->redirectToUri($nodeUri."?action=verified#petition");
    }


    /**
     * @param $identifier
     * @param array $dimensions
     * @return null|NodeInterface
     */
    private function findNodeByIdentifier($identifier, array $dimensions = []) : ?NodeInterface{
        $context = $this->contextFactory->create(['dimensions' => $dimensions]);
        $nodeData = $this->nodeDataRepository->findOneByIdentifier($identifier, $context->getWorkspace(), $context->getDimensions());

        return $nodeData ? $this->nodeFactory->createFromNodeData($nodeData, $context) : null;
    }
}

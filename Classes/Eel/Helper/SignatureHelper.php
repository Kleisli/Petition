<?php
namespace Kleisli\Petition\Eel\Helper;

use Kleisli\Petition\Domain\Repository\SignatureRepository;
use Neos\ContentRepository\Domain\Model\Node;
use Neos\Eel\ProtectedContextAwareInterface;
use Neos\Flow\Annotations as Flow;

class SignatureHelper implements ProtectedContextAwareInterface
{

    #[Flow\Inject]
    protected SignatureRepository $signatureRepository;

    public function signaturesForPetition(Node $documentNode): Iterable
    {
        return $this->signatureRepository->getPublicVerifiedByPetition($documentNode->getNodeAggregateIdentifier());
    }

    public function numberOfSignaturesForPetition(Node $documentNode): int
    {
        return $this->signatureRepository->getNumberOfVerifiedByPetition($documentNode->getNodeAggregateIdentifier());
    }

    /**
     * All methods are considered safe, i.e. can be executed from within Eel
     *
     * @param string $methodName
     * @return boolean
     */
    public function allowsCallOfMethod($methodName)
    {
        return true;
    }
}

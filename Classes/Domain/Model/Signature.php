<?php
namespace Kleisli\Petition\Domain\Model;

/*
 * This file is part of the Kleisli.Petition package.
 */

use DateTime;
use Neos\ContentRepository\Domain\Factory\NodeFactory;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\ContentRepository\Domain\Repository\NodeDataRepository;
use Neos\ContentRepository\Domain\Service\ContextFactoryInterface;
use Neos\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Exception;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;
use Neos\Flow\Utility\Algorithms;

/**
 * @Flow\Entity
 */
class Signature
{

    #[Flow\Inject]
    protected NodeDataRepository $nodeDataRepository;

    #[Flow\Inject]
    protected NodeFactory $nodeFactory;

    #[Flow\Inject]
    protected ContextFactoryInterface $contextFactory;

    /**
     * @var string
     */
    public string $petitionNodeIdentifier;

    /**
     * @var string
     */
    public string $firstName;

    /**
     * @var string
     */
    public string $lastName;

    /**
     * @var string
     */
    public string $email;

    /**
     * @var string
     */
    public string $company;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    public string $motivation = '';

    /**
     * @var bool
     */
    public bool $news = false;

    /**
     * @var bool
     */
    public bool $public = false;

    /**
     * @var string
     */
    public string $token;

    /**
     * @var DateTime
     */
    public DateTime $signedAt;

    /**
     * @var ?DateTime
     * @ORM\Column(nullable=TRUE)
     */
    public ?DateTime $verifiedAt = null;

    /**
     * @param $cause int The cause of the object initialization.
     * @see http://flowframework.readthedocs.org/en/stable/TheDefinitiveGuide/PartIII/ObjectManagement.html#lifecycle-methods
     * @throws Exception
     */
    public function initializeObject($cause)
    {
        if ($cause === ObjectManagerInterface::INITIALIZATIONCAUSE_CREATED) {
            $this->token = Algorithms::generateRandomString(16);
            $this->signedAt = new DateTime();
        }
    }


    /**
     * @return string
     */
    public function getPetitionNodeIdentifier(): string
    {
        return $this->petitionNodeIdentifier;
    }

    /**
     * @param string $petitionNodeIdentifier
     */
    public function setPetitionNodeIdentifier(string $petitionNodeIdentifier): void
    {
        $this->petitionNodeIdentifier = $petitionNodeIdentifier;
    }

    /**
     * @param array $dimensions
     * @return null|NodeInterface
     * @throws \Neos\ContentRepository\Exception\NodeConfigurationException
     * @throws \Neos\Flow\Persistence\Exception\IllegalObjectTypeException
     */
    public function getPetitionNode(array $dimensions = []) : ?NodeInterface {
        $context = $this->contextFactory->create(['dimensions' => $dimensions]);
        $nodeData = $this->nodeDataRepository->findOneByIdentifier($this->petitionNodeIdentifier, $context->getWorkspace(), $context->getDimensions());

        return $nodeData ? $this->nodeFactory->createFromNodeData($nodeData, $context) : null;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getCompany(): string
    {
        return $this->company;
    }

    /**
     * @param string $company
     */
    public function setCompany(string $company): void
    {
        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return DateTime
     */
    public function getSignedAt(): DateTime
    {
        return $this->signedAt;
    }

    /**
     * @param DateTime $signedAt
     */
    public function setSignedAt(DateTime $signedAt): void
    {
        $this->signedAt = $signedAt;
    }

    /**
     * @return DateTime
     */
    public function getVerifiedAt(): ?DateTime
    {
        return $this->verifiedAt;
    }

    /**
     * @param DateTime $verifiedAt
     */
    public function setVerifiedAt(DateTime $verifiedAt): void
    {
        $this->verifiedAt = $verifiedAt;
    }



}

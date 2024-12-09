<?php
namespace Kleisli\Petition\Domain\Repository;

/*
 * This file is part of the Kleisli.Petition package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\QueryInterface;
use Neos\Flow\Persistence\QueryResultInterface;
use Neos\Flow\Persistence\Repository;

/**
 * @Flow\Scope("singleton")
 */
class SignatureRepository extends Repository
{
    protected $defaultOrderings = ['signedAt' => QueryInterface::ORDER_DESCENDING];

    /**
     * @param string $petitionIdentifier
     * @return int
     */
    public function getNumberOfVerifiedByPetition(string $petitionIdentifier){
        $query = $this->createQuery();

        return $this->getVerifiedByPetition($petitionIdentifier)->count();
    }

    /**
     * @param string $petitionIdentifier
     * @return QueryResultInterface
     */
    public function getVerifiedByPetition(string $petitionIdentifier){
        $query = $this->createQuery();

        return $query->matching(
            $query->logicalAnd(
                [
                    $query->equals('petitionNodeIdentifier', $petitionIdentifier),
                    $query->logicalNot( $query->equals('verifiedAt', null))
                ]
            )
        )->setOrderings($this->defaultOrderings)->execute();
    }
    /**
     * @param string $petitionIdentifier
     * @return QueryResultInterface
     */
    public function getPublicVerifiedByPetition(string $petitionIdentifier){
        $query = $this->createQuery();

        return $query->matching(
            $query->logicalAnd(
                [
                    $query->equals('petitionNodeIdentifier', $petitionIdentifier),
                    $query->equals('public', true),
                    $query->logicalNot( $query->equals('verifiedAt', null))
                ]
            )
        )->setOrderings($this->defaultOrderings)->execute();
    }

}

<?php

namespace MartenaSoft\Content\Repository;

use Doctrine\ORM\QueryBuilder;
use MartenaSoft\Common\Library\CommonStatusInterface;
use MartenaSoft\Common\Repository\AbstractCommonRepository;
use MartenaSoft\Content\Entity\Content;
use MartenaSoft\Trash\Entity\TrashEntityInterface;

abstract class AbstractContentRepository extends AbstractCommonRepository
{
    public function getItemsQueryBuilder(): QueryBuilder
    {
        return $this
            ->getItemQueryBuilder()
            ->orderBy(static::getAlias().".id", "DESC")
            ;
    }

    public function getItemQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->getQueryBuilder();


        if (interface_exists(CommonStatusInterface::class) &&
            class_implements($this->getEntityName(), CommonStatusInterface::class)) {

            $queryBuilder
                ->andWhere(static::getAlias().".status=:status")
                ->setParameter("status", $this->getStatus());
        }

        if (interface_exists(TrashEntityInterface::class) &&
            class_implements($this->getEntityName(), TrashEntityInterface::class)) {

            $queryBuilder
                ->andWhere(static::getAlias().".isDeleted=:deletedStatus")
                ->setParameter("deletedStatus", false);
        }

        return $queryBuilder;
    }
}

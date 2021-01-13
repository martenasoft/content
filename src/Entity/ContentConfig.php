<?php

namespace MartenaSoft\Content\Entity;

use  Doctrine\ORM\Mapping as ORM;
use MartenaSoft\Common\Library\CommonValues;
use Symfony\Component\Validator\Constraint as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use MartenaSoft\Content\Repository\ContentConfigRepository;

/**
 * @ORM\Entity(repositoryClass="ContentConfigRepository")
 * @UniqueEntity (
 *     fields={"name"}
 * )
 */
class ContentConfig
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */

    private ?int $id = null;

    private ?int $previewInMainPage = 0;

    private ?int $paginationLimit = CommonValues::SITE_PAGINATION_LIMIT;
}
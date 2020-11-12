<?php

namespace MartenaSoft\Content\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use MartenaSoft\Content\Repository\ContentRepository;

/**
 * @ORM\Entity(repositoryClass=ContentRepository::class)
 * @UniqueEntity(
 *     fields={"name"}
 * )
 */
class Content
{
    public const STATUS_NEW = 1;
    public const STATUS_PUBLIC = 2;
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;
    
    /** 
     * @Assert\NotBlank()
     * @@ORM\Column() 
     */
    private ?string $name;


    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

}


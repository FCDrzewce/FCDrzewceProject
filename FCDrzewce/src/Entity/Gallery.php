<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="gallery")
 * @ApiResource()
 */
class Gallery
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id = null;

    /**
     * @ORM\Column(type="integer", length=128)
     */
    public ?int $user_id;

    /**
     * @ORM\Column(type="text")
     */
    public string $name;

    /**
     * @ORM\Column(type="text")
     */
    public string $description;

    /**
     * @ORM\Column(type="datetime")
     */
    public ?\DateTime $created_at = null;


    /******** METHODS ********/

    public function getId()
    {
        return $this->id;
    }

    /**
     * Prepersist gets triggered on Insert
     * @ORM\PrePersist
     */
    public function updatedTimestamps()
    {
        if ($this->created_at == null) {
            $this->created_at = new \DateTime('now');
        }
    }
}
<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\ImageController;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="image")
 * @ApiResource(
 *   normalizationContext={"groups" = {"read"}},
 *   denormalizationContext={"groups" = {"write"}},
 *   itemOperations={
 *     "get",
 *     "patch",
 *     "delete",
 *     "put",
 *     "add_image" = {
 *       "method" = "POST",
 *       "path" = "/images/add",
 *       "controller" = ImageController::class,
 *       "read"=false,
 *     },
 *   }
 * )
 */

/*#[ApiResource(
    itemOperations: ['add_image' => [
        'method' => 'POST',
        'path' => '/images/add',
        'controller' => ImageController::class,
        'read' => false
    ]],
    denormalizationContext: ['groups' => ['write']],
    formats: ["json"],
    normalizationContext: ['groups' => ['read']]
)]*/
class Image
{
    /**
     * @ORM\Id
     * @Groups({"read"})
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    public $id = null;

    /**
     * @ORM\Column(type="integer", length=128)
     * @Groups({"read", "write"})
     */
    public ?int $gallery_id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"read", "write"})
     */
    public string $path;

    /**
     * @ORM\Column(type="text")
     * @Groups({"read", "write"})
     */
    public string $reference;

    /**
     * @ORM\Column(type="text")
     * @Groups({"read", "write"})
     */
    public string $description;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"read"})
     */
    public ?DateTime $created_at = null;


    /******** METHODS ********/

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getGalleryId(): ?int
    {
        return $this->gallery_id;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->created_at;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param null $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @param int|null $gallery_id
     */
    public function setGalleryId(?int $gallery_id): void
    {
        $this->gallery_id = $gallery_id;
    }

    /**
     * @param string $reference
     */
    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @param DateTime|null $created_at
     */
    public function setCreatedAt(?DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * Prepersist gets triggered on Insert
     * @ORM\PrePersist
     */
    public function updatedTimestamps()
    {
        if ($this->created_at == null) {
            $this->created_at = new DateTime('now');
        }
    }
}
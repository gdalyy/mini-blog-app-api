<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\CreateMediaObjectAction;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class Media Object.
 *
 * @author Ghaith Daly <daly.ghaith@gmail.com>
 *
 * @ORM\Entity
 *
 * @ApiResource(
 *     iri="http://schema.org/MediaObject",
 *     normalizationContext={
 *         "groups"={"media_object:output"}
 *     },
 *     collectionOperations={
 *         "post"={
 *             "controller"=CreateMediaObjectAction::class,
 *             "deserialize"=false,
 *             "security"="is_granted('ROLE_USER') and !is_granted('ROLE_ADMIN') ",
 *             "validation_groups"={"Default", "media_object:input"},
 *             "openapi_context"={
 *                 "requestBody"={
 *                     "content"={
 *                         "multipart/form-data"={
 *                             "schema"={
 *                                 "type"="object",
 *                                 "properties"={
 *                                     "file"={
 *                                         "type"="string",
 *                                         "format"="binary"
 *                                     }
 *                                 }
 *                             }
 *                         }
 *                     }
 *                 }
 *             }
 *         },
 *         "get"={"security"="is_granted('ROLE_ADMIN')"}
 *     },
 *     itemOperations={
 *         "get"={"security"="is_granted('ROLE_ADMIN') or (user.verificationRequest and object == user.verificationRequest.image)"}
 *     }
 * )
 *
 * @Vich\Uploadable
 */
class MediaObject
{
    /**
     * @var int|null
     *
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @ORM\Id
     */
    protected ?int $id;

    /**
     * @var string|null
     *
     * @ApiProperty(iri="https://schema.org/contentUrl")
     * @Groups({"media_object:output"})
     */
    public ?string $contentUrl;

    /**
     * @var File|null
     *
     * @Assert\NotNull(groups={"media_object:input"})
     * @Vich\UploadableField(mapping="media_object", fileNameProperty="filePath")
     */
    public ?File $file = null;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     */
    public ?string $filePath;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param string|null $filePath
     * @return $this
     */
    public function setFilePath(?string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }
}
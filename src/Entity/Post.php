<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Post.
 *
 * @author Ghaith Daly <daly.ghaith@gmail.com>
 *
 * @ORM\Entity(repositoryClass=PostRepository::class)
 * @ORM\HasLifecycleCallbacks()
 *
 * @ApiResource(
 *     normalizationContext={
 *          "groups"={"post:output"}
 *     },
 *     denormalizationContext={
 *          "groups"={"post:input"}
 *     },
 *     collectionOperations={
 *         "get",
 *         "post"={
 *              "security"="is_granted('ROLE_BLOGGER')",
 *              "validation_groups"={"Default", "post:input"}
 *         }
 *     },
 *     itemOperations={
 *         "get",
 *         "put"={
 *              "security"="is_granted('ROLE_ADMIN') or (is_granted('ROLE_BLOGGER') and object.user == user)",
 *              "validation_groups"={"Default", "post:input"}
 *          }
 *     }
 * )
 */
class Post
{
    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"post:output"})
     */
    private ?int $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"post:input"})
     * @Groups({"post:output", "post:input"})
     */
    private string $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank(groups={"post:input"})
     * @Groups({"post:output", "post:input"})
     */
    private string $content;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"post:output"})
     */
    private \DateTimeImmutable $date;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"post:output"})
     */
    public User $user;

    /**
     * @ORM\PrePersist
     */
    public function makePrePersistOperations()
    {
        $this->date = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}

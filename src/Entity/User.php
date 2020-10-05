<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User.
 *
 * @author Ghaith Daly <daly.ghaith@gmail.com>
 *
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks()
 *
 * @ApiResource(
 *     normalizationContext={
 *          "groups"={"user:output"}
 *     },
 *     denormalizationContext={
 *          "groups"={"user:input"}
 *     },
 *     collectionOperations={
 *         "get"={
 *              "security"="is_granted('ROLE_ADMIN')"
 *          },
 *         "post"={
 *              "path"="/register",
 *              "validation_groups"={"Default", "user:input"},
 *              "openapi_context"={
 *     		        "tags"={"Security"},
 *                  "summary"="Register User"
 *              }
 *         }
 *     },
 *     itemOperations={
 *         "get",
 *     }
 * )
 *
 * @UniqueEntity("email")
 */
class User implements UserInterface
{
    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user:output"})
     */
    private ?int $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(groups={"user:input"})
     * @Assert\Email(groups={"user:input"})
     * @ApiProperty(iri="https://schema.org/email")
     * @Groups({"user:output", "user:input"})
     */
    private string $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank(groups={"user:input"})
     * @Groups({"user:output", "user:input"})
     */
    private string $firstname;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank(groups={"user:input"})
     * @Groups({"user:output", "user:input"})
     */
    private string $lastname;

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     *
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @var string|null The plain password
     *
     * @Assert\NotBlank(groups={"user:input"})
     * @Assert\Length(
     *     min="8",
     *     max="16",
     *     groups={"user:input"}
     * )
     * @Groups({"user:input"})
     * @SerializedName("password")
     */
    private ?string $plainPassword;

    /**
     * @var Collection|Post[]
     *
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="user", orphanRemoval=true)
     * @Groups({"user:output"})
     */
    private Collection $posts;

    /**
     * @var VerificationRequest
     *
     * @ORM\OneToOne(targetEntity=VerificationRequest::class, mappedBy="user", cascade={"persist", "remove"})
     * @Groups({"user:output"})
     */
    public ?VerificationRequest $verificationRequest = null;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function makePrePersistOperations()
    {
        $this->firstname = ucfirst($this->firstname);
        $this->lastname = ucfirst($this->lastname);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }

    public function getVerificationRequest(): ?VerificationRequest
    {
        return $this->verificationRequest;
    }

    public function setVerificationRequest(VerificationRequest $verificationRequest): self
    {
        $this->verificationRequest = $verificationRequest;

        if ($verificationRequest->getUser() !== $this) {
            $verificationRequest->setUser($this);
        }

        return $this;
    }
}

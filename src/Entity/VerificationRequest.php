<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\VerificationRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class VerificationRequest.
 *
 * @author Ghaith Daly <daly.ghaith@gmail.com>
 *
 * @ORM\Entity(repositoryClass=VerificationRequestRepository::class)
 * @ORM\HasLifecycleCallbacks()
 *
 * @ApiResource(
 *     normalizationContext={
 *          "groups"={"verification_request:output"}
 *     },
 *     denormalizationContext={
 *          "groups"={"verification_request:input"}
 *     },
 *     collectionOperations={
 *         "get"={"security"="is_granted('ROLE_ADMIN')"},
 *         "post"={"security"="!is_granted('ROLE_ADMIN')"}
 *     },
 *     itemOperations={
 *         "get"={
 *              "security"="is_granted('ROLE_ADMIN') or object.user == user"
 *          },
 *         "put"={
 *              "security"="is_granted('ROLE_ADMIN') or (object.user == user and object.status == constant('App\\Entity\\VerificationRequest::STATUS_VERIFICATION_REQUESTED'))"
 *          },
 *         "patch"={
 *              "security"="is_granted('ROLE_ADMIN') or (object.user == user and object.status == constant('App\\Entity\\VerificationRequest::STATUS_VERIFICATION_REQUESTED'))"
 *          },
 *     }
 * )
 * @ApiFilter(SearchFilter::class, properties={"status": "partial", "user": "exact"})
 * @ApiFilter(OrderFilter::class, properties={"date"}, arguments={"orderParameterName"="order"})
 *
 * @UniqueEntity("image")
 */
class VerificationRequest
{
    const STATUS_VERIFICATION_REQUESTED = 'VERIFICATION_REQUESTED';
    const STATUS_VERIFICATION_DECLINED = 'VERIFICATION_DECLINED';
    const STATUS_VERIFICATION_APPROVED = 'VERIFICATION_APPROVED';

    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"verification_request:output"})
     */
    private ?int $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Groups({"verification_request:output", "admin:input"})
     */
    public string $status = self::STATUS_VERIFICATION_REQUESTED;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"verification_request:output"})
     */
    private \DateTimeImmutable $date;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"verification_request:output", "verification_request:input"})
     */
    private ?string $message;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"admin:input"})
     */
    private ?string $rejectionReason;

    /**
     * @var MediaObject
     *
     * @ORM\ManyToOne(targetEntity=MediaObject::class)
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull(groups={"media_object:input"})
     * @ApiProperty(iri="http://schema.org/image")
     * @Groups({"verification_request:output", "verification_request:input"})
     */
    public MediaObject $image;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="verificationRequest")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"verification_request:output"})
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, array(self::STATUS_VERIFICATION_APPROVED, self::STATUS_VERIFICATION_DECLINED))) {
            throw new \InvalidArgumentException("Invalid status");
        }

        $this->status = $status;

        return $this;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getRejectionReason(): ?string
    {
        return $this->rejectionReason;
    }

    public function setRejectionReason(?string $rejectionReason): self
    {
        $this->rejectionReason = $rejectionReason;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getImage(): MediaObject
    {
        return $this->image;
    }

    public function setImage(MediaObject $image): self
    {
        $this->image = $image;

        return $this;
    }
}

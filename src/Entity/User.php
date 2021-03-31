<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email", "client")
 * @Serializer\ExclusionPolicy("ALL")
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "user_show",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "list",
 *      href = @Hateoas\Route(
 *          "user_list",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "user_delete",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "add",
 *      href = @Hateoas\Route(
 *          "user_add",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     * @OA\Property(description="The unique identifier of the user.")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose
     * @Serializer\Groups({"add_user"})
     * @Assert\NotBlank
     * @Assert\Length(
     *  min = 2,
     *  max = 255
     * )
     * @OA\Property(type="string", maxLength=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose
     * @Serializer\Groups({"add_user"})
     * @Assert\NotBlank
     * @Assert\Length(
     *  min = 2,
     *  max = 255
     * )
     * @OA\Property(type="string", maxLength=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose
     * @Serializer\Groups({"add_user"})
     * @Assert\NotBlank
     * @Assert\Email
     * @OA\Property(type="string", format="email")
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     * @OA\Property(ref=@Model(type=Client::class))
     */
    private $client;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }
}

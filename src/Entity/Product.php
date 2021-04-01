<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @Serializer\ExclusionPolicy("ALL")
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "product_show",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @OA\Property(description="The unique identifier of the product.")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     * @Serializer\Expose
     * @OA\Property(type="string", maxLength=30)
     */
    private $reference;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose
     * @OA\Property(type="string", maxLength=255)
     */
    private $designation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Expose
     * @OA\Property(type="string", maxLength=255)
     */
    private $brand;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Serializer\Expose
     */
    private $priceExclTax;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): self
    {
        $this->designation = $designation;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getPriceExclTax(): ?float
    {
        return $this->priceExclTax;
    }

    public function setPriceExclTax(?float $priceExclTax): self
    {
        $this->priceExclTax = $priceExclTax;

        return $this;
    }
}

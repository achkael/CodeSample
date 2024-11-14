<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\DTO\WarehouseLocationProductHistoryExport;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WarehouseLocationProductHistoryRepository")
 * @ORM\Table
 * @ApiResource(attributes={
 *     "access_control"="is_granted('ROLE_EMPLOYEE')",
 *     "normalization_context"={"groups"={"wlp_history_read"}},
 *     "denormalization_context"={"groups"={"wlp_history_write"}},
 *     "pagination_partial"=true,
 *     "fetch_eager"=false,
 *     "order"={"id"="DESC"}
 * },collectionOperations={
 *     "get",
 *     "export"={
 *          "method"="GET",
 *          "path"="/warehouse_location_product_histories/export",
 *          "formats"={"csv"={"text/csv"}},
 *          "pagination_enabled"=false,
 *          "output"=WarehouseLocationProductHistoryExport::class,
 *          "normalization_context"={"groups"={"wlp_history_export"}}
 *     }
 * })
 */
class WarehouseLocationProductHistory
{
    public const MVT_TYPE_ADD = 1;
    public const MVT_TYPE_MOVE = 2;
    public const MVT_TYPE_REMOVE = 3;
    public const MVT_TYPE_MODIFICATION = 4;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", name="id", length=10, options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"wlp_history_read", "admin_wlp_history_read"})
     */
    private $id;

    /**
     * @var SupplierOrderDetail
     * @ORM\ManyToOne(targetEntity="App\Entity\SupplierOrderDetail")
     * @ORM\JoinColumn(nullable=true, name="supplier_order_details_id", referencedColumnName="id")
     * @Groups({"wlp_history_read"})
     */
    private $supplierOrderDetail;

    /**
     * @var Product
     * @ORM\ManyToOne(targetEntity="App\Entity\Product")
     * @ORM\JoinColumn(nullable=false, name="id_product", referencedColumnName="id_product")
     * @Groups({"wlp_history_read", "admin_wlp_history_read"})
     */
    private $product;

    /**
     * @var WarehouseLocation|null
     * @ORM\ManyToOne(targetEntity="App\Entity\WarehouseLocation")
     * @ORM\JoinColumn(nullable=true, name="warehouse_location_id_old", referencedColumnName="id")
     * @Groups({"wlp_history_read", "admin_wlp_history_read"})
     */
    private $warehouseLocationOld;

    /**
     * @var WarehouseLocation
     * @ORM\ManyToOne(targetEntity="App\Entity\WarehouseLocation")
     * @ORM\JoinColumn(nullable=false, name="warehouse_location_id_new", referencedColumnName="id", onDelete="CASCADE")
     * @Groups({"wlp_history_read", "admin_wlp_history_read"})
     */
    private $warehouseLocationNew;

    /**
     * @var WarehouseLocationProductMvtReason
     * @ORM\ManyToOne(targetEntity="App\Entity\WarehouseLocationProductMvtReason")
     * @ORM\JoinColumn(nullable=false, name="warehouse_location_product_mvt_reason_id", referencedColumnName="id")
     * @Groups({"wlp_history_read", "admin_wlp_history_read"})
     */
    private $warehouseLocationProductMvtReason;

    /**
     * @var int
     * @ORM\Column(type="integer", length=10)
     * @Groups({"wlp_history_read", "admin_wlp_history_read"})
     */
    private $quantityOld = 0;

    /**
     * @var int
     * @ORM\Column(type="integer", length=10)
     * @Groups({"wlp_history_read", "admin_wlp_history_read"})
     */
    private $quantityNew = 0;

    /**
     * @var int
     * @ORM\Column(name="id_mvt_type", type="integer", length=1)
     * @Groups({"wlp_history_read", "admin_wlp_history_read"})
     */
    private $type = self::MVT_TYPE_ADD;

    /**
     * @var Employee|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Employee")
     * @ORM\JoinColumn(nullable=true, name="id_employee", referencedColumnName="id_employee")
     * @Groups({"wlp_history_read", "admin_wlp_history_read"})
     */
    private $employee;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", name="created_at")
     * @Groups({"wlp_history_read", "admin_wlp_history_read"})
     */
    private $createdAt;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", name="bbdate_old", nullable=true)
     * @Groups({"wlp_history_read", "admin_wlp_history_read"})
     */
    private $bbDateOld;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", name="bbdate_new", nullable=true)
     * @Groups({"wlp_history_read", "admin_wlp_history_read"})
     */
    private $bbDateNew;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSupplierOrderDetail(): ?SupplierOrderDetail
    {
        return $this->supplierOrderDetail;
    }

    public function setSupplierOrderDetail(?SupplierOrderDetail $supplierOrderDetail = null): self
    {
        $this->supplierOrderDetail = $supplierOrderDetail;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getWarehouseLocationOld(): ?WarehouseLocation
    {
        return $this->warehouseLocationOld;
    }

    public function setWarehouseLocationOld(?WarehouseLocation $warehouseLocationOld): self
    {
        $this->warehouseLocationOld = $warehouseLocationOld;

        return $this;
    }

    public function getWarehouseLocationNew(): ?WarehouseLocation
    {
        return $this->warehouseLocationNew;
    }

    public function setWarehouseLocationNew(WarehouseLocation $warehouseLocationNew): self
    {
        $this->warehouseLocationNew = $warehouseLocationNew;

        return $this;
    }

    public function getQuantityOld(): ?int
    {
        return $this->quantityOld;
    }

    public function setQuantityOld(int $quantityOld): self
    {
        $this->quantityOld = $quantityOld;

        return $this;
    }

    public function getQuantityNew(): ?int
    {
        return $this->quantityNew;
    }

    public function setQuantityNew(int $quantityNew): self
    {
        $this->quantityNew = $quantityNew;

        return $this;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): self
    {
        $this->employee = $employee;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getWarehouseLocationProductMvtReason(): ?WarehouseLocationProductMvtReason
    {
        return $this->warehouseLocationProductMvtReason;
    }

    public function setWarehouseLocationProductMvtReason(WarehouseLocationProductMvtReason $warehouseLocationProductMvtReason): self
    {
        $this->warehouseLocationProductMvtReason = $warehouseLocationProductMvtReason;

        return $this;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getBbDateOld(): ?\DateTime
    {
        return $this->bbDateOld;
    }

    public function setBbDateOld(?\DateTime $bbDateOld): self
    {
        $this->bbDateOld = $bbDateOld;

        return $this;
    }

    public function getBbDateNew(): ?\DateTime
    {
        return $this->bbDateNew;
    }

    public function setBbDateNew(?\DateTime $bbDateNew): self
    {
        $this->bbDateNew = $bbDateNew;

        return $this;
    }
}

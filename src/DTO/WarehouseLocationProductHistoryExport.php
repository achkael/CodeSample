<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Serializer\Annotation\Groups;

final class WarehouseLocationProductHistoryExport
{
    /**
     * @var string
     * @Groups({"wlp_history_export"})
     */
    public $productName;

    /**
     * @var string
     * @Groups({"wlp_history_export"})
     */
    public $employeeName;

    /**
     * @var string
     * @Groups({"wlp_history_export"})
     */
    public $createdAt;

    /**
     * @var string
     * @Groups({"wlp_history_export"})
     */
    public $fromLocation;

    /**
     * @var string
     * @Groups({"wlp_history_export"})
     */
    public $fromType;

    /**
     * @var string
     * @Groups({"wlp_history_export"})
     */
    public $toLocation;

    /**
     * @var string
     * @Groups({"wlp_history_export"})
     */
    public $toType;

    /**
     * @var string
     * @Groups({"wlp_history_export"})
     */
    public $city;

    /**
     * @var string
     * @Groups({"wlp_history_export"})
     */
    public $movementReason;

    /**
     * @var string
     * @Groups({"wlp_history_export"})
     */
    public $type;

    /**
     * @var int
     * @Groups({"wlp_history_export"})
     */
    public $quantityOld;

    /**
     * @var int
     * @Groups({"wlp_history_export"})
     */
    public $quantityNew;

    public function toArray(): array
    {
        return (array) $this;
    }
}

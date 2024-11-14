<?php

namespace App\DTO;

use App\Entity\Organization;

class WarehouseLoactionProductHistoryFilter
{
    public const DEFAULT_PAGE = 1;
    public const DEFAULT_ITEMS_PER_PAGE = 10;
    public const DEFAULT_ORDER = ['id' => 'DESC'];

    /** @var int */
    public $page;

    /** @var int */
    public $itemsPerPage;

    /** @var string */
    public $orderBy;

    /** @var array */
    public $createdAt;

    /** @var string */
    public $createdAtOperator;

    /** @var string|null */
    public $search;

    /** @var int|null */
    public $employeeId;

    /** @var string|null */
    public $productName;

    /** @var int|null */
    public $productId;

    /** @var int|null */
    public $type;

    /** @var string|null */
    public $warehouseLocationOld;

    /** @var string|null */
    public $warehouseLocationNew;

    /** @var Organization */
    public $organization;

    /** @var string */
    public $externalId;

    /** @var bool */
    public $isPaginationEnabled = true;

    /** @var int|null */
    public $movementReason;

    /** @var int|null */
    public $warehouse;

    public ?string $ean13;

    public ?int $locationType;

    public function __construct()
    {
        $this->createdAt[] = new \DateTime('NOW - 3 month');
        $this->createdAtOperator = '>';
    }
}

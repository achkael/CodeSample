<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\WarehouseLocationProductHistoryExport;
use App\Entity\WarehouseLocationProductHistory;

final class WarehouseLocationProductHistoryExportDataTransformer implements DataTransformerInterface
{
    public const TYPE_NAME = [
        WarehouseLocationProductHistory::MVT_TYPE_ADD => 'Add',
        WarehouseLocationProductHistory::MVT_TYPE_MOVE => 'Move',
        WarehouseLocationProductHistory::MVT_TYPE_REMOVE => 'Remove',
    ];

    /**
     * @param WarehouseLocationProductHistory $object
     * @param string                          $to
     * @param array                           $context
     *
     * @return array
     */
    public function transform($object, string $to, array $context = []): array
    {
        $wlphExport = new WarehouseLocationProductHistoryExport();
        $wlphExport->productName = $object->getProduct() ? $object->getProduct()->getName() : null;
        $wlphExport->employeeName = $object->getEmployee() ? $object->getEmployee()->getFullName() : null;
        $wlphExport->createdAt = $object->getCreatedAt() ? $object->getCreatedAt()->format('Y-m-d H:i:s') : null;
        $wlphExport->type = self::TYPE_NAME[$object->getType()];
        $wlphExport->movementReason = $object->getWarehouseLocationProductMvtReason()->getName();
        $wlphExport->quantityOld = $object->getQuantityOld();
        $wlphExport->quantityNew = $object->getQuantityNew();

        if ($warehouseLocationOld = $object->getWarehouseLocationOld()) {
            $wlphExport->fromLocation = $warehouseLocationOld->getStandardLocation();
            $wlphExport->fromType = $warehouseLocationOld->getWarehouseLocationType()->getName();
        }
        if ($warehouseLocationNew = $object->getWarehouseLocationNew()) {
            $wlphExport->toLocation = $warehouseLocationNew->getStandardLocation();
            $wlphExport->toType = $warehouseLocationNew->getWarehouseLocationType()->getName();
            $wlphExport->city = $warehouseLocationNew->getWarehouse() ? $warehouseLocationNew->getWarehouse()->getName() : null;
        }

        return $wlphExport->toArray();
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return WarehouseLocationProductHistoryExport::class === $to && $data instanceof WarehouseLocationProductHistory;
    }
}

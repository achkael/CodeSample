<?php

namespace App\Repository;

use App\Entity\Employee;
use App\Entity\Warehouse;
use App\Entity\WarehouseLocationType;
use Doctrine\ORM\QueryBuilder;

class WarehouseLocationRepository extends SelectDataRepository
{
    public function addSelectDataOptions(QueryBuilder $queryBuilder, Employee $employee, array $options = null): void
    {
        if (!isset($options['organization'])) {
            return;
        }

        $queryBuilder
            ->andWhere('o.organization = :organization')
            ->setParameter('organization', $options['organization']);
    }

    public function findDestinationsSimple(Warehouse $defaultWarehouse, array $filters = []): array
    {
        $queryBuilder = $this->createQueryBuilder('wl')
            ->select(
                'wlp.id AS warehouseLocationProductId',
                'wlp.bestBeforeDate',
                'wlp.quantity',
                'wl.id AS warehouseLocationId',
                'wlt.name AS locationTypeName',
                'wl.location',
                'wl.alley',
                'wl.rank',
                'wl.level',
                'wl.emplacement'
            )
            ->leftJoin('wl.warehouseLocationProducts', 'wlp')
            ->innerJoin('wl.warehouseLocationType', 'wlt')
            ->andWhere('wl.warehouse = :warehouse')
            ->andWhere('wl.active = 1')
            ->setParameter('warehouse', $filters['warehouse'] ?? $defaultWarehouse)
            ->orderBy('wl.location', 'ASC')
        ;

        if (isset($filters['product'])) {
            $queryBuilder
                ->andWhere('wlp.product = :product')
                ->setParameter('product', $filters['product'])
            ;
        }

        if (isset($filters['locationType'])) {
            $queryBuilder
                ->andWhere('wlt.id = :locationType')
                ->setParameter('locationType', $filters['locationType'])
            ;
        }

        if (isset($filters['quantity'])) {
            if (0 === (int) $filters['quantity']) {
                $wlps = $this->findLocationsWithQuantity();

                $queryBuilder
                    ->andWhere('wlp.warehouseLocation NOT IN (:wlps)')
                    ->setParameter('wlps', $wlps);
            } else {
                $queryBuilder
                    ->andWhere('wlp.quantity = :quantity')
                    ->setParameter('quantity', $filters['quantity'])
                ;
            }
        }

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function findEmptyLocationsSimple(int $locationType, Warehouse $defaultWarehouse, array $filters = [])
    {
        $queryBuilder = $this->createQueryBuilder('wl')
            ->select(
                'wlp.id AS warehouseLocationProductId',
                'wlp.bestBeforeDate',
                'wlp.quantity',
                'wl.id AS warehouseLocationId',
                'wlt.name AS locationTypeName',
                'wl.location',
                'wl.alley',
                'wl.rank',
                'wl.level',
                'wl.emplacement'
            )
            ->leftJoin('wl.warehouseLocationProducts', 'wlp')
            ->innerJoin('wl.warehouseLocationType', 'wlt')
            ->andWhere('wlp.warehouseLocation IS NULL')
            ->andWhere('wlt.id = :locationType')
            ->andWhere('wl.warehouse = :warehouse')
            ->andWhere('wl.active = 1')
            ->setParameter('warehouse', $filters['warehouse'] ?? $defaultWarehouse)
            ->setParameter('locationType', $locationType)
        ;

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function findLocationsWithQuantity(): array
    {
        return $this->createQueryBuilder('wl')
            ->leftJoin('wl.warehouseLocationProducts', 'wlp')
            ->andWhere('wlp.quantity > 0')
            ->getQuery()
            ->getArrayResult();
    }

    public function findQuarantineLocationId(Warehouse $warehouse): int
    {
        return (int) $this->createQueryBuilder('wl')
            ->select('wl.id')
            ->andWhere('wl.warehouse = :warehouse')
            ->andWhere('wl.warehouseLocationType = :quarantine')
            ->andWhere('wl.active = 1')
            ->setParameters([
                'warehouse' => $warehouse,
                'quarantine' => WarehouseLocationType::TYPE_QUARANTINE,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}

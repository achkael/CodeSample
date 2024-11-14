<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\PaginationExtension;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\DTO\WarehouseLoactionProductHistoryFilter;
use App\Entity\WarehouseLocationProductHistory;
use App\Resolver\ContextResolver;
use App\Utils\Symfony\Component\HttpFoundation\ParameterBag as AppParameterBag;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

final class WarehouseLocationProductHistoryDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface, ContextAwareCollectionDataProviderInterface
{
    private ManagerRegistry $managerRegistry;
    private RequestStack $request;
    private ContextResolver $contextResolver;
    private PaginationExtension $paginationExtension;

    public function __construct(ManagerRegistry $managerRegistry, RequestStack $request, ContextResolver $contextResolver, PaginationExtension $paginationExtension)
    {
        $this->managerRegistry = $managerRegistry;
        $this->request = $request;
        $this->contextResolver = $contextResolver;
        $this->paginationExtension = $paginationExtension;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return WarehouseLocationProductHistory::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        $filter = $this->mapRequestDataToFilterDTO(AppParameterBag::fromSfParameterBag($this->request->getCurrentRequest()->query));

        if (array_intersect(['admin_wlp_history_export'], $context['groups'])) {
            $filter->isPaginationEnabled = false;
        }
        $manager = $this->managerRegistry->getManagerForClass($resourceClass);
        $repository = $manager->getRepository($resourceClass);
        $queryBuilder = $repository->getHistoryList($filter);

        $this->paginationExtension->applyToCollection($queryBuilder, new QueryNameGenerator(), $resourceClass, $operationName, $context);

        if ($this->paginationExtension instanceof QueryResultCollectionExtensionInterface
            && $this->paginationExtension->supportsResult($resourceClass, $operationName, $context)) {
            return $this->paginationExtension->getResult($queryBuilder, $resourceClass, $operationName, $context);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    private function mapRequestDataToFilterDTO(AppParameterBag $parameterBag): WarehouseLoactionProductHistoryFilter
    {
        $filter = new WarehouseLoactionProductHistoryFilter();
        $filter->organization = $this->contextResolver->resolveOrganization();
        $filter->orderBy = $parameterBag->get('order', WarehouseLoactionProductHistoryFilter::DEFAULT_ORDER);
        $filter->itemsPerPage = $parameterBag->getInt('itemsPerPage', WarehouseLoactionProductHistoryFilter::DEFAULT_ITEMS_PER_PAGE);
        $filter->page = $parameterBag->getInt('page', WarehouseLoactionProductHistoryFilter::DEFAULT_PAGE);
        $filter->type = $parameterBag->getInt('type', null);
        $filter->locationType = $parameterBag->getInt('locationType', null);
        $filter->productName = $parameterBag->getAlnum('product_langs_name', null);
        $filter->warehouseLocationNew = $parameterBag->getAlnum('warehouseLocationNew_location', null);
        $filter->warehouseLocationOld = $parameterBag->getAlnum('warehouseLocationOld_location', null);
        $filter->employeeId = $parameterBag->getInt('employee_id', null);
        $filter->search = $parameterBag->getAlnum('search', null);
        $filter->movementReason = $parameterBag->getInt('movement_reason', null);
        $filter->warehouse = $parameterBag->getInt('warehouse', null);
        $filter->productId = $parameterBag->getAlnum('productId');

        if ($parameterBag->has('createdAt')) {
            $createdAt = $parameterBag->get('createdAt');
            $filter->createdAt = [];
            if (\count($createdAt) > 1) {
                $filter->createdAtOperator = 'between';
                foreach ($createdAt as $key => $value) {
                    $filter->createdAt[] = new \DateTime($value);
                }
            } else {
                foreach ($createdAt as $key => $value) {
                    $filter->createdAt[] = new \DateTime($value);
                    switch ($key) {
                        case 'gt':
                        case 'strictly_after':
                            $filter->createdAtOperator = '>';
                            break;
                        case 'lt':
                        case 'strictly_before':
                            $filter->createdAtOperator = '<';
                            break;
                        case 'lte':
                        case 'before':
                            $filter->createdAtOperator = '<=';
                            break;
                        case 'gte':
                        case 'after':
                            $filter->createdAtOperator = '>=';
                            break;
                        default:
                            $filter->createdAtOperator = '>';
                    }
                }
            }
        }

        return $filter;
    }
}

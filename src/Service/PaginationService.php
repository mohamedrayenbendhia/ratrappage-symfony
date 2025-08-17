<?php

namespace App\Service;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PaginationService
{
    public function paginate(QueryBuilder $queryBuilder, int $page = 1, int $limit = 10): array
    {
        $paginator = new Paginator($queryBuilder);
        
        $totalItems = count($paginator);
        $totalPages = (int) ceil($totalItems / $limit);
        
        $queryBuilder
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);
        
        $items = $queryBuilder->getQuery()->getResult();
        
        return [
            'items' => $items,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalItems' => $totalItems,
            'itemsPerPage' => $limit,
            'hasNextPage' => $page < $totalPages,
            'hasPreviousPage' => $page > 1,
            'nextPage' => $page < $totalPages ? $page + 1 : null,
            'previousPage' => $page > 1 ? $page - 1 : null,
        ];
    }
}

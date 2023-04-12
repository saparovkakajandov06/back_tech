<?php
namespace App\Repositories\Interfaces;

interface TransactionRepositoryInterface
{
    public function getGroupByUser(int $userId, string $dateFrom, string $dateTo, string $transactionGroup, string $cur, int $offset, int $limit);
    
    public function getTotals(int $userId, string $cur);
    
    public function getRefBonuses(int $parentId, int $transactionsOffset, int $transactionsLimit);

    public function getRefBonusesTotal(int $parentId);
}
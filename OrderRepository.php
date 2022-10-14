<?php

class OrderRepository
{
    public function getRecentOrderCountAfterTargetTime(string $accountId, Carbon $time): int
    {
        return DB::table('orders')
            ->where('account', $accountId)
            ->where('created_at', '>=', $time)
            ->count();
    }

    public function insertOrder(array $data): int
    {
        DB::table('orders')->insert($data);
    }
}
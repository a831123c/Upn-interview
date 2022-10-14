<?php

class OrderProcessor
{
    public function __construct(private BillerInterface $biller, private OrderRepository $orderRepository)
    {
    }

    public function process(Order $order)
    {
        if ($this->getRecentOrderCount($order) > 0)
        {
            throw new Exception('Duplicate order likely.');
        }

        $this->biller->bill($order->account->id, $order->amount);

        $this->orderRepository->insertOrder([
            'account'    => $order->account->id,
            'amount'     => $order->amount,
            'created_at' => Carbon::now(),
        ]);
    }

    protected function getRecentOrderCount(Order $order)
    {
        return $this->orderRepository->getRecentOrderCountAfterTargetTime(
            accountId: $order->account->id,
            time: Carbon::now()->subMinutes(5)
        );
    }
}
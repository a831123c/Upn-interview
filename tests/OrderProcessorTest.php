<?php

class OrderProcessorTest extends TestCase
{
    use RefreshDataBase;

    public function testProcessWithDuplicate()
    {
        Carbon::setTestNow(Carbon::parse('2022-04-26 10:20:00'));
        $account = Account::factory()->create();
        $order = Order::factory()->for($account)->createMany([
            [
                'created_at' => '2022-04-26 10:10:00'
            ],
            [
                'created_at' => '2022-04-26 10:15:00'
            ]
        ]);

        $processor = resolve(OrderProcessor::class);
        $this->expectExceptionMessage('Duplicate order likely.');
        $processor->process($order);
    }

    public function testProcess()
    {
        Carbon::setTestNow(Carbon::parse('2022-04-26 10:20:00'));
        $account = Account::factory()->create();
        $order = Order::factory()->for($account)->create([
            'created_at' => '2022-04-26 10:10:00'
        ]);

        $mock = Mockery::mock('ClassImplementBillerInterface');
        $mock->shouldReceive('bill')->once();

        $this->app->instance('BillerInterface', $mock);
        $processor = resolve(OrderProcessor::class);
        $processor->process($order);

        $this->assertDatabaseHas(Order::class, [
            'account' => $account->getKey(),
            'amount' => $order->account,
        ]);
    }

}
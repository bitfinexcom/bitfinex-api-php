<?php

use BFX\RESTv2;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class rest2Test extends TestCase
{
    public function testGetStatus()
    {
        $historyContainer = [];
        $client = $this->createClientWithHistory(
            [new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/200_status_response.json'))],
            $historyContainer
        );

        $rest2 = new RESTv2('test', 'test', '', '', '', true, null, null, $client);
        $status = $rest2->status();

        $this->assertEquals(1, $status[0]);
    }

    public function testGetUserinfo()
    {
        $historyContainer = [];
        $client = $this->createClientWithHistory(
            [new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/200_userInfo_response.json'))],
            $historyContainer
        );

        $rest2 = new RESTv2('test', 'test', '', '', '', true, null, null, $client);
        $userinfo = $rest2->userInfo();

        $this->assertEquals('12345', $userinfo->getId());
        $this->assertEquals('test@test.com', $userinfo->getEmail());
        $this->assertEquals('sub_bit_test', $userinfo->getUsername());
        $this->assertEquals('UTC', $userinfo->getTimezone());
        $this->assertEquals(null, $userinfo->getIsPaperTradeEnabled());
    }

    public function testGetMovements()
    {
        $historyContainer = [];
        $client = $this->createClientWithHistory(
            [new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/200_movements_response.json'))],
            $historyContainer
        );

        $rest2 = new RESTv2('test', 'test', '', '', '', true, null, null, $client);
        $movements = $rest2->movements(null, 1169348774000, 2569348774009);
        $this->assertEquals('13105603', $movements[0]->getId());
    }

    public function testGetLedgers()
    {
        $historyContainer = [];
        $client = $this->createClientWithHistory(
            [new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/200_ledgerEntry_response.json'))],
            $historyContainer
        );

        $rest2 = new RESTv2('test', 'test', '', '', '', true, null, null, $client);
        $ledgers = $rest2->ledgers(null, null, 1569348774000, 1569348774009);

        $this->assertEquals('2531822314', $ledgers->getId());
        $this->assertEquals('USD', $ledgers->getCurrency());
        $this->assertEquals('1573521810000', $ledgers->getMts());
        $this->assertEquals(0, $ledgers->getBalance());
        $this->assertEquals('Settlement @ 185.79 on wallet margin', $ledgers->getDescription());
        $this->assertEquals('margin', $ledgers->getWallet());
    }

    public function testGetKeyPermission()
    {
        $historyContainer = [];
        $client = $this->createClientWithHistory(
            [new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/200_keyPermission_response.json'))],
            $historyContainer
        );

        $rest2 = new RESTv2('test', 'test', '', '', '', true, null, null, $client);
        $keyPermission = $rest2->keyPermissions();

        $this->assertEquals('account', $keyPermission[0]->getKey());
        $this->assertEquals(true, $keyPermission[0]->getRead());
        $this->assertEquals(false, $keyPermission[0]->getWrite());
    }

    public function testGetDepositAddress()
    {
        $historyContainer = [];
        $client = $this->createClientWithHistory(
            [new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/200_depositAddress_response.json'))],
            $historyContainer
        );

        $rest2 = new RESTv2('test', 'test', '', '', '', true, null, null, $client);
        $depositAddress = $rest2->getDepositAddress(['opRenew'=>0]);

        $this->assertEquals('1637603347308', $depositAddress->getMts());
        $this->assertEquals('acc_dep', $depositAddress->getType());
        $this->assertEquals(null, $depositAddress->getMessageID());
    }

    public function testGetGenerateToken()
    {
        $historyContainer = [];
        $client = $this->createClientWithHistory(
            [new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/200_generateToken_response.json'))],
            $historyContainer
        );

        $rest2 = new RESTv2('test', 'test', '', '', '', false, null, null, $client);
        $generateToken = $rest2->generateToken(['scope'=>'api']);

        $this->assertEquals('pub:api:2d83311f-ff68-42b1-9dce-fbba53bced21-read', $generateToken[0]);
    }

    public function testGetWithdraw()
    {
        $historyContainer = [];
        $client = $this->createClientWithHistory(
            [new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/200_withdraw_response.json'))],
            $historyContainer
        );

        $rest2 = new RESTv2('test', 'test', '', '', '', true, null, null, $client);
        $withdraw = $rest2->withdraw('');

        $this->assertEquals('1568742390999', $withdraw->getMts());
        $this->assertEquals('acc_wd-req', $withdraw->getType());
        $this->assertEquals(null, $withdraw->getMessageID());
        $this->assertEquals([13080092,null,"ethereum",null,"exchange",0.01,null,null,0.00135], $withdraw->getNotifyInfo());
    }

    public function testGetPayInvoiceCreate()
    {
        $historyContainer = [];
        $client = $this->createClientWithHistory(
            [new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/200_payInvoiceCreate_response.json'))],
            $historyContainer
        );

        $rest2 = new RESTv2('test', 'test', '', '', '', true, null, null, $client);
        $payInvoiceCreate = $rest2->payInvoiceCreate([]);

        $this->assertEquals('a6761c8b-468f-40ad-a522-cc5e41c39757', $payInvoiceCreate->id);
    }

    public function testGetPayInvoiceList()
    {
        $historyContainer = [];
        $client = $this->createClientWithHistory(
            [new Response(200, [], file_get_contents(__DIR__ . '/Fixtures/200_payInvoiceList_response.json'))],
            $historyContainer
        );

        $rest2 = new RESTv2('test', 'test', '', '', '', true, null, null, $client);
        $payInvoiceList = $rest2->payInvoiceList([]);

        $this->assertEquals('a6761c8b-468f-40ad-a522-cc5e41c39757', $payInvoiceList[0]->id);
    }

    private function createClientWithHistory(array $responses, array &$historyContainer): Client
    {
        $handlerStack = HandlerStack::create(
            new MockHandler([
                ...$responses,
            ])
        );
        $history = Middleware::history($historyContainer);
        $handlerStack->push($history);
        return new Client(['handler' => $handlerStack]);
    }

}

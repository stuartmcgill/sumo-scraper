<?php

declare(strict_types=1);

namespace unit\DomainService\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use StuartMcGill\SumoScraper\DomainService\Api\BashoService;

class BashoServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var Client|MockInterface */
    private $httpClient;

    public function setUp(): void
    {
        $this->httpClient = Mockery::mock(Client::class);
    }

    /** @test */
    public function fetch(): void
    {
        $response = Mockery::mock(Response::class);
        $response
            ->expects('getBody->__toString')
            ->once()
            ->andReturn('{"TEST_KEY": "TEST_VALUE"}');

        $promise = Mockery::mock(PromiseInterface::class);
        $promise->expects('wait')->once()->andReturn($response);

        $this->httpClient
            ->expects('getAsync')
            ->once()
            ->with('https://sumo-api.com/api/basho/202303/banzuke/First')
            ->andReturn($promise);

        $bashoService = new BashoService($this->httpClient, ['apiRateLimit' => 0]);
        $bashoData = $bashoService->fetch(2023, 3, ['First']);

        $this->assertEquals(
            expected: (object)['TEST_KEY' => 'TEST_VALUE'],
            actual: $bashoData,
        );
    }
}

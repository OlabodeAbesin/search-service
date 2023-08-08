<?php

namespace Tests\Feature;

use App\Gateways\ParkAndRideRankerGateway;
use App\Gateways\ParkingSpaceRankerGateway;
use App\ParkAndRide;
use App\ParkingSpace;
use App\ThirdParty\ParkingSpaceHttpService;
use App\ThirdParty\TimeoutException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ParkingSpaceRankerGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function testParkingSpaceRanker()
    {
        // TODO Part 2) Done

        $parkingSpace7 = ParkingSpace::factory()->create(['id' => 7]);
        $parkingSpace8 = ParkingSpace::factory()->create(['id' => 8]);
        $parkingSpace9 = ParkingSpace::factory()->create(['id' => 9]);

        $gateway = app(ParkingSpaceRankerGateway::class);

        $result = $gateway->rank([$parkingSpace7, $parkingSpace8, $parkingSpace9]);

        $this->assertEquals([$parkingSpace7, $parkingSpace8, $parkingSpace9], $result);
    }

    public function testSlowService()
    {
        // TODO Part 4) Done
        // Create a sample ParkingSpace instance using a factory
        $parkingSpace = ParkingSpace::factory()->create();

        // Mock the ParkingSpaceHttpService to simulate a slow response
        $mockedParkingSpaceHttpService = $this->mock(ParkingSpaceHttpService::class);
        $mockedParkingSpaceHttpService->shouldReceive('getRanking')->andThrow(new TimeoutException());

        // Create the ParkingSpaceRankerGateway instance with the mocked ParkingSpaceHttpService
        $parkingSpaceRankerGateway = new ParkingSpaceRankerGateway($mockedParkingSpaceHttpService);

        // Call the rank method with the sample ParkingSpaceHttpService data
        $rankedItems = $parkingSpaceRankerGateway->rank([$parkingSpace->toArray()]);

        // Verify that the method still returns a result (default or empty array)
        $this->assertIsArray($rankedItems);
    }
}

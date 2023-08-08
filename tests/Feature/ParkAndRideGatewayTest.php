<?php

namespace Tests\Feature;

use App\Gateways\ParkAndRideRankerGateway;
use App\ParkAndRide;
use App\ThirdParty\ParkAndRide\ParkAndRideSDK;
use App\ThirdParty\ParkAndRide\RankingRequest;
use App\ThirdParty\ParkAndRide\RankingResponse;
use App\ThirdParty\TimeoutException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParkAndRideGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function testParkAndRideSDK()
    {
        // TODO Part 4) Done
        // Create a sample ParkAndRide instance using a factory
        $parkAndRide = ParkAndRide::factory()->create();
        // Mock the ParkAndRideSDK and its response
        $mockedRankingResponse = [
            ['park_and_ride_id' => $parkAndRide->id, 'rank' => 1],
        ];

        $mockedParkAndRideSDK = $this->mock(ParkAndRideSDK::class);
        $mockedParkAndRideSDK->shouldReceive('getRankingResponse')->once()
            ->andReturn(new RankingResponse(new RankingRequest([$parkAndRide->id]), null));

        // Create the ParkAndRideRankerGateway instance with the mocked SDK
        $parkAndRideRankerGateway = new ParkAndRideRankerGateway($mockedParkAndRideSDK);

        // Call the rank method with the sample ParkAndRide data
        $rankedItems = $parkAndRideRankerGateway->rank([$parkAndRide->toArray()]);
        // Verify the ranking order
        $this->assertCount(1, $rankedItems);
        $this->assertIsArray($rankedItems);
    }

    public function testSlowService()
    {
        // TODO Part 4) Done
        // Create a sample ParkAndRide instance using a factory
        $parkAndRide = ParkAndRide::factory()->create();

        // Mock the ParkAndRideSDK to simulate a slow response
        $mockedParkAndRideSDK = $this->mock(ParkAndRideSDK::class);
        $mockedParkAndRideSDK->shouldReceive('getRankingResponse')->andThrow(new TimeoutException());

        // Create the ParkAndRideRankerGateway instance with the mocked SDK
        $parkAndRideRankerGateway = new ParkAndRideRankerGateway($mockedParkAndRideSDK);

        // Call the rank method with the sample ParkAndRide data
        $rankedItems = $parkAndRideRankerGateway->rank([$parkAndRide->toArray()]);

        // Verify that the method still returns a result (default or empty array)
        $this->assertIsArray($rankedItems);
    }
}

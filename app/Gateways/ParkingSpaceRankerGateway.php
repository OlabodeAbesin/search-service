<?php

namespace App\Gateways;

use App\ThirdParty\ParkAndRide\RankingRequest;
use App\ThirdParty\ParkingSpaceHttpService;
use Illuminate\Support\Facades\Log;

class ParkingSpaceRankerGateway
{
    // TODO Part 2) create the parking space ranker gateway using the ParkingSpaceHttpService (Done)

    private $parkingSpaceHttpService;

    public function __construct(ParkingSpaceHttpService $parkingSpaceHttpService)
    {
        //Not necessary in php8.1
        $this->parkingSpaceHttpService = $parkingSpaceHttpService;
    }

    public function rank(array $items)
    {
        $rankedItems = [];
        try {
            $keyedItems = [];
            foreach ($items as $item) {
                $keyedItems[$item['id']] = $item;
            }

            $parkingSpaceIds = array_column($keyedItems, 'id');
            $rankedResponse = $this->parkingSpaceHttpService->getRanking(json_encode($parkingSpaceIds));
            // Convert the sorted IDs from the response to an array
            $ranking = json_decode($rankedResponse->getBody(), true);

            Log::info('Got ranking: ' . json_encode($ranking));

            foreach ($ranking as $rank) {
                $rankedItems[] = $keyedItems[$rank];
            }
        } catch (\Throwable $th) {
            Log::info('ParkingSpaceHttpService error: ', [$th]);
        }
        return $rankedItems;
    }
}

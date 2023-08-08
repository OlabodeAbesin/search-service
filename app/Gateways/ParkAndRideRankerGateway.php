<?php

namespace App\Gateways;

use App\ThirdParty\ParkAndRide\ParkAndRideSDK;
use App\ThirdParty\ParkAndRide\RankingRequest;
use Illuminate\Support\Facades\Log;

class ParkAndRideRankerGateway
{
    private $parkAndRide;

    public function __construct(ParkAndRideSDK $parkAndRide)
    {
        $this->parkAndRide = $parkAndRide;
    }

    public function rank(array $items)
    {
        $rankedItems = [];
        try {
            $keyedItems = [];
            foreach ($items as $item) {
                $keyedItems[$item['id']] = $item;
            }
            $rankedResponse = $this->parkAndRide->getRankingResponse(new RankingRequest(array_keys($keyedItems)))->getResult();
            $arr = array_column($rankedResponse, 'rank');
            array_multisort($arr, SORT_ASC, $rankedResponse);
            $ranking = array_column($rankedResponse, 'park_and_ride_id');

            Log::info('Got ranking: '.json_encode($ranking));

            foreach ($ranking as $rank) {
                $rankedItems[] = $keyedItems[$rank];
            }
        } catch (\Throwable $th) {
            $rankedItems = $items;
            Log::info('ParkAndRideSDK threw an error: ', [$th]);
        }

        return $rankedItems;
    }
}

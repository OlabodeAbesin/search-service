<?php

namespace App\Http\Controllers;

use App\Gateways\ParkAndRideRankerGateway;
use App\Gateways\ParkingSpaceRankerGateway;
use App\Http\Requests\SearchDetailsRequest;
use App\SearchService;
use App\ThirdParty\ParkAndRide\ParkAndRideSDK;
use App\ThirdParty\ParkingSpaceHttpService;

class SearchController extends Controller
{
    private $parkingSpaceRankerGateway;
    private $parkAndRideRankerGateway;
    private $parkingSpaceHttpService;
    private $parkAndRideSDK;

    public function __construct(
        ParkingSpaceRankerGateway $parkingSpaceRankerGateway,
        ParkAndRideRankerGateway $parkAndRideRankerGateway,
        ParkingSpaceHttpService $parkingSpaceHttpService,
        ParkAndRideSDK $parkAndRideSDK
    ) {
        $this->parkingSpaceRankerGateway = $parkingSpaceRankerGateway;
        $this->parkAndRideRankerGateway = $parkAndRideRankerGateway;
        $this->parkingSpaceHttpService = $parkingSpaceHttpService;
        $this->parkAndRideSDK = $parkAndRideSDK;
    }


    public function index(
        SearchService $searchService,
        SearchDetailsRequest $searchDetailsRequest
    ) {
         // @todo Part 3) validate lat long (Done)
         $boundingBox = $searchService->getBoundingBox($searchDetailsRequest->lat, $searchDetailsRequest->lng, 5);
         $parkingSpaces = $searchService->searchParkingSpaces($boundingBox);
         $parkAndRide = $searchService->searchParkAndRide($boundingBox);
 
         // Rank 'park and rides' using ParkAndRideRankerGateway
         $rankedParkAndRide = $this->parkAndRideRankerGateway->rank($parkAndRide);

         // Rank the parking spaces based on the returned IDs using ParkingSpaceRankerGateway
         $rankedParkingSpaces = $this->parkingSpaceRankerGateway->rank($parkingSpaces);         
 
         // Merge the ranked 'park and rides' and parking spaces
         $resultArray = array_merge($rankedParkAndRide, $rankedParkingSpaces);
 
         // @todo Part 3)  N+1 queries inside the resource transformer (Done)
         return \App\Http\Resources\Location::collection(collect($resultArray));
    }

    public function details(SearchService $searchService, SearchDetailsRequest $searchDetailsRequest)
    {
        // @todo Part 3) validate lat long
        $boundingBox = $searchService->getBoundingBox($searchDetailsRequest->lat, $searchDetailsRequest->lng, 5);
        $parkingSpaces = $searchService->searchParkingSpaces($boundingBox);
        $parkAndRide = $searchService->searchParkAndRide($boundingBox);

        return response()->json($this->formatLocations($parkAndRide, $parkingSpaces)); /*@todo Part 1 Done) */ 
    }

    private function formatLocations(array $parkAndRide, array $parkingSpaces)
    {
        //@todo Part 1) format 'park and rides' and parking spaces for response (Done)
        $formattedLocations = [];

        // Format 'park and rides'
        foreach ($parkAndRide as $parkAndRideLocation) {
            $description = "Park and Ride to {$parkAndRideLocation->attraction_name}. (approx {$parkAndRideLocation->minutes_to_destination} minutes to destination)";
            $locationName = $parkAndRideLocation->location_description;

            $formattedLocations[] = [
                'description' => $description,
                'location_name' => $locationName,
            ];
        }
        // Format parking spaces
        foreach ($parkingSpaces as $parkingSpace) {
            $description = "Parking space with {$parkingSpace->no_of_spaces} bays: {$parkingSpace->space_details}";
            $locationName = "$parkingSpace->street_name, $parkingSpace->city";

            $formattedLocations[] = [
                'description' => $description,
                'location_name' => $locationName,
            ];
        }

        return $formattedLocations;
    }
}
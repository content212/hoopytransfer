<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class GoogleMapsApiController extends \App\Http\Controllers\Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $origin = $request->get('origin');
        $destination = $request->get('destination');

        $response = Http::get('https://maps.googleapis.com/maps/api/directions/json?origin=' . $origin . '&destination=' . $destination . '&key=AIzaSyCIc76iOhe0hi46KWEJwGI8jaruPUlO43o');
        $distance = $response->json()['routes'][0]['legs'][0]['distance']['value'];
        $duration = $response->json()['routes'][0]['legs'][0]['duration']['value'];
        $polyline = $response->json()['routes'][0]['overview_polyline']['points'];
        return response()->json(
            [
                'distance' => $distance,
                'duration' => $duration,
                'polyline' => GoogleMapsApiController::decode_polyline($polyline),
            ]
        );
    }

    function  decode_polyline($value)
    {
        $index = 0;
        $points = array();
        $lat = 0;
        $lng = 0;

        while ($index < strlen($value)) {
            $b;
            $shift = 0;
            $result = 0;
            do {
                $b = ord(substr($value, $index++, 1)) - 63;
                $result |= ($b & 0x1f) << $shift;
                $shift += 5;
            } while ($b > 31);
            $dlat = (($result & 1) ? ~($result >> 1) : ($result >> 1));
            $lat += $dlat;

            $shift = 0;
            $result = 0;
            do {
                $b = ord(substr($value, $index++, 1)) - 63;
                $result |= ($b & 0x1f) << $shift;
                $shift += 5;
            } while ($b > 31);
            $dlng = (($result & 1) ? ~($result >> 1) : ($result >> 1));
            $lng += $dlng;

            $points[] = array('lat' => $lat / 100000, 'lng' => $lng / 100000);
        }

        return $points;
    }
}

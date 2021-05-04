<?php

namespace App;

//use Geocoder\Laravel\ProviderAndDumperAggregator as Geocoder;
//use Geocoder;
use Geocoder\Laravel\Facades\Geocoder as Geocoder;

class AddressHelper
{

    public static function decodeLatLong($addressInput) {
        $address = $addressInput['address1'];
        $address .= isset($addressInput['address2']) ? " " . $addressInput['address2'] : "";
        $address .= ", " . $addressInput['city'] . ", " . $addressInput['state'] . " " . $addressInput['zip'];
        $address .= ", " . $addressInput['country'];

        //$geocoder = new Geocoder();
        return app('geocoder')->doNotCache()->geocode('Los Angeles, CA')->get();

        //return $geocoder->getCoordinatesForAddress($address);
        //return ['latitude' => '5', 'longitude' => '5'];
    }
}

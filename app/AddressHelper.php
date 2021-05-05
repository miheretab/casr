<?php

namespace App;


class AddressHelper
{

    public static function makeAddress($addressInput) {
        $address = $addressInput['address1'];
        $address .= isset($addressInput['address2']) ? " " . $addressInput['address2'] : "";
        $address .= ", " . $addressInput['city'] . ", " . $addressInput['state'] . " " . $addressInput['zip'];
        $address .= ", " . $addressInput['country'];

        return $address;
    }
}

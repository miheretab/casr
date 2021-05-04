<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\AddressHelper;

class CasrTest extends TestCase
{
    /**
     * A basic unit test register.
     *
     * @return void
     */
    public function test_decode_lat_long()
    {
        $addressInput = [
            'address1' => 'Rock Heven Way',
            'address2' => '#125',
            'city' => 'Sterling',
            'state' => 'VA',
            'country' => 'USA',
            'zip' => 20166,
        ];
        $latLong = AddressHelper::decodeLatLong($addressInput);
        $this->assertTrue(isset($latLong['latitude']));
    }

}

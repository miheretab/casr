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
    public function test_address()
    {
        $addressInput = [
            'address1' => 'Rock Heven Way',
            'address2' => '#125',
            'city' => 'Sterling',
            'state' => 'VA',
            'country' => 'USA',
            'zip' => 20166,
        ];
        $addressString = AddressHelper::makeAddress($addressInput);
        $this->assertEquals($addressString, 'Rock Heven Way #125, Sterling, VA 20166, USA');
    }

}

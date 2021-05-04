<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Client;
use App\AddressHelper;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ClientsCollection;

class UsersController extends Controller
{
    /**
     * index
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $clients = Client::with('users')->paginate();
        return response()->json(new ClientsCollection($clients));
    }

    /**
     * register
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request) {
        $clientInput = $request->all();
        $clientInput['email'] = isset($clientInput['user']) && isset($clientInput['user']['email']) ? $clientInput['user']['email'] : null;

        $validator = Validator::make($clientInput, [
            'name' => 'required',
            'address1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'phoneNo1' => 'required',
            'zipCode' => 'required|integer',
            'user.firstName' => 'required',
            'user.lastName' => 'required',
            'email' => 'required|email|unique:users',
            'user.phone' => 'required',
            'user.password' => ['required', Password::min(6)->mixedCase()->numbers()->symbols()],
            'user.passwordConfirmation' => 'required|same:user.password',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $clientInput['client_name'] = $clientInput['name'];
        $clientInput['phone_no1'] = $clientInput['phoneNo1'];
        $clientInput['phone_no2'] = $clientInput['phoneNo2'];
        $clientInput['zip'] = $clientInput['zipCode'];
        $clientInput['start_validity'] = Carbon::today();
        $clientInput['end_validity'] = Carbon::today()->addDays(15);

        $latLong = AddressHelper::decodeLatLong($clientInput);
        $clientInput['latitude'] = $latLong['latitude'];
        $clientInput['longitude'] = $latLong['longitude'];

        $client = Client::create($clientInput);

        $userInput = $clientInput['user'];
        $userInput['client_id'] = $client->id;
        $userInput['first_name'] = $userInput['firstName'];
        $userInput['last_name'] = $userInput['lastName'];
        $userInput['profile_uri'] = "/profile?id=" . $client->id;
        $userInput['last_password_reset'] = Carbon::now()->timestamp;
        User::create($userInput);

        return response()->json(['success' => true]);
    }

}

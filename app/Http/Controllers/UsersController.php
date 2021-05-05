<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;
use Illuminate\Support\Str;
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
        $clients = Client::with('users');

        if ($request->query('name')) {
            $clients = $clients->where('client_name', 'like', '%'.$request->query('name').'%');
        }

        if ($request->query('address1')) {
            $clients = $clients->where('address1', 'like', '%'.$request->query('address1').'%');
        }

        if ($request->query('address2')) {
            $clients = $clients->where('address2', 'like', '%'.$request->query('address2').'%');
        }

        if ($request->query('city')) {
            $clients = $clients->where('city', 'like', '%'.$request->query('city').'%');
        }

        if ($request->query('state')) {
            $clients = $clients->where('state', 'like', '%'.$request->query('state').'%');
        }

        if ($request->query('country')) {
            $clients = $clients->where('country', 'like', '%'.$request->query('country').'%');
        }

        if ($request->query('zipCode')) {
            $clients = $clients->where('zip', $request->query('zipCode'));
        }

        if ($request->query('phoneNo1')) {
            $clients = $clients->where('phone_no1', 'like', '%'.$request->query('phoneNo1').'%');
        }

        if ($request->query('phoneNo2')) {
            $clients = $clients->where('phone_no2', 'like', '%'.$request->query('phoneNo2').'%');
        }

        if ($request->query('status')) {
            $clients = $clients->where('status', $request->query('status'));
        }

        //sort by any column, by default it'll be asc
        if ($request->query('sort')) {
            $direction = $request->query('direction') ? $request->query('direction') : 'ASC';
            $clients = $clients->orderBy(Str::snake($request->query('sort')), $direction);
        }

        $clients = $clients->paginate();

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

        $addressString = AddressHelper::makeAddress($clientInput);
        $latLong = [];
        try {
            $latLong = \Geocoder::geocode($addressString);
            $latLong = $latLong->get()->toArray();
        } catch (\RedisException $ex) {
            $latLong = \Geocoder::doNotCache()->geocode($addressString)->get();
            $latLong = $latLong->toArray();
        }

        //uncomment if validation for latitude needed.
        if (!isset($latLong['lat'])) {
            //return response()->json(['error' => $latLong], 422);
            $latLong = ['lat' => 37.33, 'lng' => -122.03];//put default just for test till we got valid map api key
        }

        $clientInput['latitude'] = $latLong['lat'];
        $clientInput['longitude'] = $latLong['lng'];

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

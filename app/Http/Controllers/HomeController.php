<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request){

// Decode JSON into an associative array
$data = json_decode($request, true);

// Access the batches array
$batches = $data['batches'];

// Iterate through the batches and subscribers
foreach ($batches as $batch) {
    $subscribers = $batch['subscribers'];

    foreach ($subscribers as $subscriber) {
        // Access individual subscriber details
        $email = $subscriber['email'];
        $name = isset($subscriber['name']) ? $subscriber['name'] : 'No Name Provided';
        $timeZone = $subscriber['time_zone'];

        // Output details
        return "Email: $email, Name: $name, Time Zone: $timeZone\n";
    }
}
}
}

<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function viewImport()
    {
        return view('import');
    }

    public function parseImport(Request $request) {
        $path = $request->file('csv_file')->getRealPath();
        $data = array_map('str_getcsv', file($path));

        foreach ($data as $key => $line)
        {
            $customer = Customer::where('email', trim($line[2]))->first();
            if (!$customer) {
                $customer = new Customer();
            }

            $customer->first_name = isset($line[0]) ? trim($line[0]) : null;
            $customer->last_name = isset($line[1]) ? trim($line[1]) : null;
            $customer->email = isset($line[2]) ? trim($line[2]) : null;
            $customer->mobile_number = isset($line[3]) ? trim($line[3]) : null;
            $customer->save();
        }

        return Redirect::to('home')->with('success', 'Customers imported successfully.');
    }
}

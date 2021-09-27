<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $rules = array(
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:customers',
            'mobile_number' => 'required',
        );

        $messages = [
            'first_name.required' => 'First Name is required',
            'last_name.required' => 'Last Name  is required',
            'email.required' => 'Email is required',
            'mobile_number.required' => 'Mobile Number is required',
        ];

        $validator = Validator::make( $request->toArray(), $rules, $messages );

        if ( !$validator->passes() ) {
            return response()->json($validator->errors()->first(), 400);
        }

        try {
            $newCustomer = new Customer();
            $newCustomer->first_name = $request->first_name;
            $newCustomer->last_name = $request->last_name;
            $newCustomer->email = $request->email;
            $newCustomer->mobile_number = $request->mobile_number;
            $newCustomer->save();

            return response()->json($newCustomer, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Customer $customer, Request $request)
    {
        try {
            $customer->first_name = $request->first_name;
            $customer->last_name = $request->last_name;
            $customer->email = $request->email;
            $customer->mobile_number = $request->mobile_number;
            $customer->save();

            return response()->json($customer, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();

            return response()->json('Customer Deleted', 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    public function getCustomers(Request $request)
    {
        $search_str = trim($request->q);
        try {
            $customers = Customer::when($search_str, function ($query, $search_str) {
                return $query->where('first_name', 'LIKE', '%'.$search_str.'%')
                    ->orWhere('last_name', 'LIKE', '%'.$search_str.'%')
                    ->orWhere('email', 'LIKE', '%'.$search_str.'%')
                    ->orWhere('mobile_number', 'LIKE', '%'.$search_str.'%');
            })->paginate(10);
            return response()->json($customers, 200);
        } catch (\Exception $e) {
            return response($e->getMessage(), 400);
        }
    }
}

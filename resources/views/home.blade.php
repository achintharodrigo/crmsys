@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex col-sm-12">
                    <div class="mr-auto">
                        {{ __('Dashboard') }}
                    </div>
                    <div>
                        <a href="{{ route('import.view') }}">
                            {{ __('Import Customers') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <customer-table-component></customer-table-component>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

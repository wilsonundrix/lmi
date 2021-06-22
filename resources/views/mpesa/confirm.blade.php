@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        {{ __('Confirm Payment') }}
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('confirm_pay') }}">
                            @csrf
                            <div class="form-group row">
                                <label for="transactionID" class="col-md-4 col-form-label text-md-right">{{ __('transactionID') }}</label>
                                <div class="col-md-6">
                                    <input id="transactionID" type="text" class="form-control" name="transactionID" required value="PFI39WSTFP">
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Confirm Payment') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

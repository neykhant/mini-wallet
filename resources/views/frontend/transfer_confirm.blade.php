@extends('frontend.layouts.app')
@section('title', 'Transfer Confirmation')
@section('content')
<div class="transfer">
    <div class="card ">
        <div class="card-body">
            <form action="{{ url('transfer/confirm') }}" method="GET">

                <div class="form-group">
                    <label for="" class="mb-0"><strong>Form</strong></label>
                    <p class="mb-1 text-muted ">{{ $authUser->name }}</p>
                    <p class="mb-1 text-muted ">{{ $authUser->phone }}</p>
                </div>
                <div class="form-group">
                    <label for="" class="mb-0"><strong>To</strong></label>
                    <p class="mb-1 text-muted"> {{$to_phone}} </p>
                </div>
                <div class="form-group">
                <label for="" class="mb-0"><strong>Amount (MMK) </strong></label>
                    <p class="mb-1 text-muted"> {{number_format($amount)}} </p>
                </div>
                <div class="form-group">
                <label for="" class="mb-0"><strong>Description</strong></label>
                    <p class="mb-1 text-muted"> {{$description}} </p>
                </div>
                <button type="submit" class="btn btn-theme btn-block mt-5"> Continue</button>
            </form>
        </div>
    </div>
</div>
@endsection
@extends('frontend.layouts.app')
@section('title', 'Transfer')
@section('content')
<div class="transfer">
    <div class="card ">
        <div class="card-body">
            <form action="{{ url('transfer/confirm') }}" method="POST" autocomplete="off">
                @csrf

                <div class="form-group">
                    <label for="">From</label>
                    <p class="mb-1 text-muted ">{{ $authUser->name }}</p>
                    <p class="mb-1 text-muted ">{{ $authUser->phone }}</p>
                </div>
                <div class="form-group">
                    <label for="">To</label>
                    <input type="text" name="to_phone" class="form-control" value="{{ old('to_phone') }}">
                    @error('to_phone')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="">Amount (MMK)</label>
                    <input type="number" name="amount" class="form-control" value="{{ old('amount') }}">
                    @error('amount')
                        <span class="text-danger">{{$message}}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="">Description</label>
                    <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                </div>
                <button type="submit" class="btn btn-theme btn-block mt-5"> Continue</button>
            </form>
        </div>
    </div>
</div>
@endsection
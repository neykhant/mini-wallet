@extends('frontend.layouts.app')
@section('title', 'Transaction')
@section('content')
<div class="transaction">

    <div class="card mb-2">
        <div class="card-body p-2">
            <div class="row">
                <div class="col-6">
                    <!-- <div class="input-group my-2">
                        <div class="input-group-prepend">
                            <label class="input-group-text p-1">Type</label>
                        </div>
                        <select class="custom-select">
                            <option value="">All</option>
                            <option value="1">Income</option>
                            <option value="2">Expense</option>
                        </select>
                    </div> -->
                </div>

                <div class="col-6">
                    <div class="input-group my-2">
                        <div class="input-group-prepend">
                            <label class="input-group-text p-1">Type</label>
                        </div>
                        <select class="custom-select type">
                            <option value="">All</option>
                            <option value="1" @if(request()->type == 1) selected @endif >Income</option>
                            <option value="2" @if(request()->type == 2 ) selected @endif >Expense</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="infinite-scroll">
        @foreach( $transactions as $transaction )
        <a href="{{ url('transaction/' . $transaction->trx_id ) }}">
            <div class="card mb-2">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-1">Trx_Id : {{ $transaction->trx_id}}</h6>
                        <p class="mb-1 @if($transaction->type == 1) text-success @elseif($transaction->type == 2) text-danger @endif ">
                            {{ $transaction->amount }} <small>MMK</small>
                        </p>
                    </div>
                    <p class="mb-1 text-muted">
                        @if($transaction->type == 1)
                        From
                        @elseif($transaction->type == 2)
                        To
                        @endif

                        {{ $transaction->source ? $transaction->source->name : ''}}
                    </p>
                    <p class="text-muted mb-1">
                        {{$transaction->created_at}}
                    </p>
                </div>
            </div>
        </a>
        @endforeach
        {{ $transactions->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
    $('ul.pagination').hide();
    $(function() {
        $('.infinite-scroll').jscroll({
            autoTrigger: true,
            loadingHtml: '<div class="text-center"><img src="/images/loading.gif" alt="Loading..." /></div>',
            padding: 0,
            nextSelector: '.pagination li.active + li a',
            contentSelector: 'div.infinite-scroll',
            callback: function() {
                $('ul.pagination').remove();
            }
        });

        $('.type').change(function() {
            var type = $('.type').val();
            history.pushState(null, '', `?type=${type}`);
            window.location.reload();
            // alert(type);
        });
    });
</script>
@endsection
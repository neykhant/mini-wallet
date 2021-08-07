@if($errors->has('fail'))
<!-- @foreach($errors->all() as $error) -->
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{$errors->first('fail')}}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<!-- @endforeach -->
@endif

<!-- {{$errors}} -->
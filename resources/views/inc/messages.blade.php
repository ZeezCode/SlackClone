@if(count($errors) > 0)
    @foreach($errors->all() as $error)
        <div class="container alert alert-danger">
            {{$error}}
        </div>
    @endforeach
@endif

<?php
/**
 * Two checks for $success below because
 * if you return a view, you'll get $success
 * but if you return a redirect you'll get Session::get('success')
 * It doesn't make sense to me but at least I figured it out
 */
?>
@if(isset($success))
    <div class="container alert alert-success">
        {{$success}}
    </div>
@endif
@if(Session::has('success'))
    <div class="container alert alert-success">
        {{Session::get('success')}}
    </div>
@endif

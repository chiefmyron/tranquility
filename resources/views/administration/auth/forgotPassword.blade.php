@extends('administration.login')

@section('content')
<h1>{{ trans('administration.login_forgot_password') }}</h1>

<p>{{ trans('administration.login_reset_password_text') }}</p>

@include('administration.errors.list')

{!! Form::open(['url' => 'administration/auth/forgot-password']) !!}
    <fieldset>
        <div class="form-group">
			{!! Form::label('email', 'Email address:') !!}
			{!! Form::email('email', null, ['class' => 'form-control', 'required', 'autofocus']) !!}	
			{!! FormError::inline('email', Session::get('messages')) !!}
        </div>
        
        <div class="login-block"> 
            <a href="{!! url('administration/auth') !!}" class="btn">{{ trans('administration.common_cancel') }}</a> 
            {!! Form::submit(trans('administration.login_reset_password'), ['class' => 'btn btn-primary pull-right'])  !!}
        </div> 
    </fieldset>
{!! Form::close() !!}

@endsection


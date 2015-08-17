@extends('administration.login')

@section('content')
<h1>{{ trans('administration.login_heading_login') }}</h1>

<p>{{ trans('administration.login_welcome_text') }}</p>

@include('administration.errors.list')

{!! Form::open(['url' => 'administration/auth/login']) !!}
    <fieldset>
        <div class="form-group">
			{!! Form::label('email', 'Email address:') !!}
			{!! Form::email('email', null, ['class' => 'form-control', 'required', 'autofocus']) !!}	
			{!! FormError::inline('email', Session::get('messages')) !!}
        </div>

        <div class="form-group">
			{!! Form::label('password', 'Password:') !!}
			{!! Form::password('password', ['class' => 'form-control', 'required']) !!}	
			{!! FormError::inline('password', Session::get('messages')) !!}
        </div>
        
        <div class="checkbox">
            <label>
                {!! Form::checkbox('remember', 1, null, ['id' => 'remember']) !!} {{ trans('administration.login_remember_me') }}
            </label>
		</div>
        
        <div class="form-group">
            {!! Form::submit(trans('administration.login_heading_login'), ['class' => 'btn btn-primary form-control']) !!}
        </div>

        <div class="form-group">
            <hr />
            <a href="/administration/auth/forgot-password">{{ trans('administration.login_forgot_password') }}</a>
        </div>
    </fieldset>
{!! Form::close() !!}

@endsection


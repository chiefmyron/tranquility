@extends('administration.login')

@section('content')
<h1>{{ trans('administration.login_heading_login') }}</h1>

<p>{{ trans('administration.login_welcome_text') }}</p>

@include('administration._partials.errors', ['messages' => Session::get('messages')])

    <fieldset>
        {!! Form::open(['url' => 'administration/auth/login']) !!}
        @include('administration.auth._partials.forms.form-login')
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
        {!! Form::close() !!}
    </fieldset>
@endsection


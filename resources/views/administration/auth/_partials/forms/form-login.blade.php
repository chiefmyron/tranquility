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
					{!! Form::open(['url' => 'administration/auth/ajax', 'class' => 'ajax-submit']) !!}
                    <div class="modal-header">
						<a class="close" data-dismiss="modal" aria-label="{{ trans('administration.common_cancel') }}"><span aria-hidden="true">&times;</span></a>
						<h3 class="modal-title" id="modal-dialog-title">{{ trans('administration.login_heading_login') }}</h3>
					</div>
					<div class="modal-body">
                        <div id="dialog-process-message-container"></div>
                        @include('administration.auth._partials.forms.form-login')
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{ trans('administration.common_cancel') }}</button>
                        <button type="submit" class="btn btn-primary" id="dialog-submit">{{ trans('administration.login_heading_login') }}</button>
					</div>
                    {!! Form::close() !!}
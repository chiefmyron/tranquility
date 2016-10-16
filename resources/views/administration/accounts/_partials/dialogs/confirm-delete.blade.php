					<div class="modal-header">
						<button class="close" data-dismiss="modal" aria-label="{{ trans('administration.common_cancel') }}"><span aria-hidden="true">&times;</span></button>
						<h3 class="modal-title" id="modal-dialog-title">{{ trans('administration.common_heading_delete_confirm') }}</h3>
					</div>
					<div class="modal-body">
                        @if (!is_null($account))
						{!! trans('administration.accounts_message_delete_confirmation_single', ['name' => '<strong>'.$account->name.'</strong>']) !!}
                        @else
                        {!! trans('administration.accounts_message_delete_confirmation_multiple', ['count' => '<strong>'.count($selectedIds).'</strong>']) !!}
                        @endif
					</div>
					<div class="modal-footer">
                        @if (!is_null($account))
						{!! Form::open(array('action' => 'Administration\AccountsController@delete')) !!}
                        @else
                        {!! Form::open(array('action' => 'Administration\AccountsController@delete', 'class' => 'ajax-submit')) !!}
                        @endif
                        @foreach ($selectedIds as $id)
                        {!! Form::hidden('id[]', $id) !!}
                        @endforeach
						<button type="submit" class="btn btn-primary" id="dialog-submit">{{ trans('administration.common_command_delete_confirm') }}</button>
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{ trans('administration.common_cancel') }}</button>
                        {!! Form::close() !!}
					</div>
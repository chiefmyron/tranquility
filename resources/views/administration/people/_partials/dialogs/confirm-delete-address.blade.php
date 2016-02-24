					<div class="modal-header">
						<button class="close" data-dismiss="modal" aria-label="{{ trans('administration.common_cancel') }}"><span aria-hidden="true">&times;</span></button>
						<h3 class="modal-title" id="modal-dialog-title">{{ trans('administration.common_heading_delete_confirm') }}</h3>
					</div>
					<div class="modal-body">
						{!! trans('administration.people_message_delete_address_confirmation') !!}
					</div>
					<div class="modal-footer">
						{!! Form::open(array('action' => array('Administration\PeopleController@deleteAddress', $parentId), 'class' => 'ajax-submit')) !!}
                        {!! Form::hidden('parentId', $parentId) !!}
                        {!! Form::hidden('id', $id) !!}
						<button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{ trans('administration.common_cancel') }}</button>
						<button type="submit" class="btn btn-primary" id="dialog-submit">{{ trans('administration.common_command_delete_confirm') }}</button>
                        {!! Form::close() !!}
					</div>
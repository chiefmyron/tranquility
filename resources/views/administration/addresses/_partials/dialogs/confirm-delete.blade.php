					<div class="modal-header">
						<button class="close" data-dismiss="modal" aria-label="{{ trans('administration.common_cancel') }}"><span aria-hidden="true">&times;</span></button>
						<h3 class="modal-title" id="modal-dialog-title">{{ trans('administration.common_heading_delete_confirm') }}</h3>
					</div>
					<div class="modal-body">
						{!! trans('administration.address_message_delete_address_confirmation') !!}
					</div>
					<div class="modal-footer">
						{!! Form::open(array('action' => array('Administration\AddressController@delete', $type, $id), 'class' => 'ajax-submit')) !!}
						<button type="submit" class="btn btn-primary" id="dialog-submit">{{ trans('administration.common_command_delete_confirm') }}</button>
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{ trans('administration.common_cancel') }}</button>
                        {!! Form::close() !!}
					</div>
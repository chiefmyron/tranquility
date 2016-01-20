					<div class="modal-header">
						<button class="close" data-dismiss="modal" aria-label="{{ trans('administration.common_cancel') }}"><span aria-hidden="true">&times;</span></button>
						<h3 class="modal-title" id="modal-dialog-title">{{ trans('administration.common_heading_delete_confirm') }}</h3>
					</div>
					<div class="modal-body">
                        @if (!is_null($person))
						{!! trans('administration.people_message_delete_confirmation_single', ['name' => '<strong>'.$person->firstName.' '.$person->lastName.'</strong>']) !!}
                        @else
                        {!! trans('administration.people_message_delete_confirmation_multiple', ['count' => '<strong>'.count($selectedIds).'</strong>']) !!}
                        @endif
					</div>
					<div class="modal-footer">
                        {!! Form::open(array('action' => 'Administration\PeopleController@delete')) !!}
                        @foreach ($selectedIds as $id)
                        {!! Form::hidden('id[]', $id) !!}
                        @endforeach
						<button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{ trans('administration.common_cancel') }}</button>
						
                        @if (!is_null($person))
						<button type="submit" class="btn btn-primary" id="dialog-submit">{{ trans('administration.common_command_delete_confirm') }}</button>
                        @else
                        <button type="button" class="btn btn-primary ajax-submit" id="dialog-submit">{{ trans('administration.common_command_delete_confirm') }}</button>
                        @endif
                        {!! Form::close() !!}
					</div>
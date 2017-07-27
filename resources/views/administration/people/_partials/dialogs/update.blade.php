<div class="modal-header">
    <button class="close" data-dismiss="modal" aria-label="{{ trans('administration.common_cancel') }}"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="modal-dialog-title">{{ trans('administration.people_heading_update', ['name' => $person->getFullName()]) }}</h3>
</div>
{!! Form::model($person, ['action' => 'Administration\PeopleController@store', 'class' => 'ajax-submit']) !!}
<div class="modal-body">
    <div id="process-message-container"></div>
    @include('administration.people._partials.forms.create')
    {!! Form::hidden('id', $person->id) !!}
</div>
<div class="modal-footer">
    {!! Form::submit(trans('administration.people_command_update'), ['class' => 'btn btn-primary pull-right', 'id' => 'dialog-submit']) !!}
    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">{{ trans('administration.common_cancel') }}</button>
</div>
{!! Form::close() !!}
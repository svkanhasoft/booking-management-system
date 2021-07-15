<div class="form-group {{ $errors->has('user_id') ? 'has-error' : ''}}">
    <label for="user_id" class="control-label">{{ 'User Id' }}</label>
    <input class="form-control" name="user_id" type="number" id="user_id" value="{{ isset($organizationshift->user_id) ? $organizationshift->user_id : ''}}" >
    {!! $errors->first('user_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('start_time') ? 'has-error' : ''}}">
    <label for="start_time" class="control-label">{{ 'Start Time' }}</label>
    <input class="form-control" name="start_time" type="time" id="start_time" value="{{ isset($organizationshift->start_time) ? $organizationshift->start_time : ''}}" >
    {!! $errors->first('start_time', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('end_time') ? 'has-error' : ''}}">
    <label for="end_time" class="control-label">{{ 'End Time' }}</label>
    <input class="form-control" name="end_time" type="time" id="end_time" value="{{ isset($organizationshift->end_time) ? $organizationshift->end_time : ''}}" >
    {!! $errors->first('end_time', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>

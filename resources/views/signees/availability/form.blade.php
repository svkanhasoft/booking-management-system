<div class="form-group {{ $errors->has('user_id') ? 'has-error' : ''}}">
    <label for="user_id" class="control-label">{{ 'User Id' }}</label>
    <input class="form-control" name="user_id" type="number" id="user_id" value="{{ isset($availability->user_id) ? $availability->user_id : ''}}" >
    {!! $errors->first('user_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('day_name') ? 'has-error' : ''}}">
    <label for="day_name" class="control-label">{{ 'Day Name' }}</label>
    <input class="form-control" name="day_name" type="text" id="day_name" value="{{ isset($availability->day_name) ? $availability->day_name : ''}}" >
    {!! $errors->first('day_name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('shift_id') ? 'has-error' : ''}}">
    <label for="shift_id" class="control-label">{{ 'Shift Id' }}</label>
    <input class="form-control" name="shift_id" type="number" id="shift_id" value="{{ isset($availability->shift_id) ? $availability->shift_id : ''}}" >
    {!! $errors->first('shift_id', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>

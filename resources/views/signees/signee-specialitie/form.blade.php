<div class="form-group {{ $errors->has('user_id') ? 'has-error' : ''}}">
    <label for="user_id" class="control-label">{{ 'User Id' }}</label>
    <input class="form-control" name="user_id" type="number" id="user_id" value="{{ isset($signeespecialitie->user_id) ? $signeespecialitie->user_id : ''}}" >
    {!! $errors->first('user_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('speciality_id') ? 'has-error' : ''}}">
    <label for="speciality_id" class="control-label">{{ 'Speciality Id' }}</label>
    <input class="form-control" name="speciality_id" type="number" id="speciality_id" value="{{ isset($signeespecialitie->speciality_id) ? $signeespecialitie->speciality_id : ''}}" >
    {!! $errors->first('speciality_id', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>

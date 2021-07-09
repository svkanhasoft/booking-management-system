<div class="form-group {{ $errors->has('user_id') ? 'has-error' : ''}}">
    <label for="user_id" class="control-label">{{ 'User Id' }}</label>
    <input class="form-control" name="user_id" type="number" id="user_id" value="{{ isset($speciality->user_id) ? $speciality->user_id : ''}}" >
    {!! $errors->first('user_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('speciality_name') ? 'has-error' : ''}}">
    <label for="speciality_name" class="control-label">{{ 'Speciality Name' }}</label>
    <input class="form-control" name="speciality_name" type="text" id="speciality_name" value="{{ isset($speciality->speciality_name) ? $speciality->speciality_name : ''}}" >
    {!! $errors->first('speciality_name', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>

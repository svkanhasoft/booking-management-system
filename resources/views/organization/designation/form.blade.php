<div class="form-group {{ $errors->has('user_id') ? 'has-error' : ''}}">
    <label for="user_id" class="control-label">{{ 'User Id' }}</label>
    <input class="form-control" name="user_id" type="number" id="user_id" value="{{ isset($designation->user_id) ? $designation->user_id : ''}}" >
    {!! $errors->first('user_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('designation_name') ? 'has-error' : ''}}">
    <label for="designation_name" class="control-label">{{ 'Designation Name' }}</label>
    <input class="form-control" name="designation_name" type="text" id="designation_name" value="{{ isset($designation->designation_name) ? $designation->designation_name : ''}}" >
    {!! $errors->first('designation_name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('created_by') ? 'has-error' : ''}}">
    <label for="created_by" class="control-label">{{ 'Created By' }}</label>
    <input class="form-control" name="created_by" type="number" id="created_by" value="{{ isset($designation->created_by) ? $designation->created_by : ''}}" >
    {!! $errors->first('created_by', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('updated_by') ? 'has-error' : ''}}">
    <label for="updated_by" class="control-label">{{ 'Updated By' }}</label>
    <input class="form-control" name="updated_by" type="number" id="updated_by" value="{{ isset($designation->updated_by) ? $designation->updated_by : ''}}" >
    {!! $errors->first('updated_by', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>

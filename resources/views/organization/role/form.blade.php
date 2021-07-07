<div class="form-group {{ $errors->has('user_id') ? 'has-error' : ''}}">
    <label for="user_id" class="control-label">{{ 'User Id' }}</label>
    <input class="form-control" name="user_id" type="number" id="user_id" value="{{ isset($role->user_id) ? $role->user_id : ''}}" >
    {!! $errors->first('user_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('role_name') ? 'has-error' : ''}}">
    <label for="role_name" class="control-label">{{ 'Role Name' }}</label>
    <input class="form-control" name="role_name" type="text" id="role_name" value="{{ isset($role->role_name) ? $role->role_name : ''}}" >
    {!! $errors->first('role_name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('created_by') ? 'has-error' : ''}}">
    <label for="created_by" class="control-label">{{ 'Created By' }}</label>
    <input class="form-control" name="created_by" type="number" id="created_by" value="{{ isset($role->created_by) ? $role->created_by : ''}}" >
    {!! $errors->first('created_by', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('updated_by') ? 'has-error' : ''}}">
    <label for="updated_by" class="control-label">{{ 'Updated By' }}</label>
    <input class="form-control" name="updated_by" type="number" id="updated_by" value="{{ isset($role->updated_by) ? $role->updated_by : ''}}" >
    {!! $errors->first('updated_by', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>

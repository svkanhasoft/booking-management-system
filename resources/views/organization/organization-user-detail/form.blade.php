<div class="form-group {{ $errors->has('user_id') ? 'has-error' : ''}}">
    <label for="user_id" class="control-label">{{ 'User Id' }}</label>
    <input class="form-control" name="user_id" type="number" id="user_id" value="{{ isset($organizationuserdetail->user_id) ? $organizationuserdetail->user_id : ''}}" >
    {!! $errors->first('user_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('contact_number') ? 'has-error' : ''}}">
    <label for="contact_number" class="control-label">{{ 'Contact Number' }}</label>
    <input class="form-control" name="contact_number" type="text" id="contact_number" value="{{ isset($organizationuserdetail->contact_number) ? $organizationuserdetail->contact_number : ''}}" >
    {!! $errors->first('contact_number', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('role_id') ? 'has-error' : ''}}">
    <label for="role_id" class="control-label">{{ 'Role Id' }}</label>
    <input class="form-control" name="role_id" type="number" id="role_id" value="{{ isset($organizationuserdetail->role_id) ? $organizationuserdetail->role_id : ''}}" >
    {!! $errors->first('role_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('designation_id') ? 'has-error' : ''}}">
    <label for="designation_id" class="control-label">{{ 'Designation Id' }}</label>
    <input class="form-control" name="designation_id" type="number" id="designation_id" value="{{ isset($organizationuserdetail->designation_id) ? $organizationuserdetail->designation_id : ''}}" >
    {!! $errors->first('designation_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('created_by') ? 'has-error' : ''}}">
    <label for="created_by" class="control-label">{{ 'Created By' }}</label>
    <input class="form-control" name="created_by" type="number" id="created_by" value="{{ isset($organizationuserdetail->created_by) ? $organizationuserdetail->created_by : ''}}" >
    {!! $errors->first('created_by', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('updated_by') ? 'has-error' : ''}}">
    <label for="updated_by" class="control-label">{{ 'Updated By' }}</label>
    <input class="form-control" name="updated_by" type="number" id="updated_by" value="{{ isset($organizationuserdetail->updated_by) ? $organizationuserdetail->updated_by : ''}}" >
    {!! $errors->first('updated_by', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>

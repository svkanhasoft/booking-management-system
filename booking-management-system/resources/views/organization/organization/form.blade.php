<div class="form-group {{ $errors->has('user_id') ? 'has-error' : ''}}">
    <label for="user_id" class="control-label">{{ 'User Id' }}</label>
    <input class="form-control" name="user_id" type="number" id="user_id" value="{{ isset($organization->user_id) ? $organization->user_id : ''}}" >
    {!! $errors->first('user_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('organization_name') ? 'has-error' : ''}}">
    <label for="organization_name" class="control-label">{{ 'Organization Name' }}</label>
    <input class="form-control" name="organization_name" type="text" id="organization_name" value="{{ isset($organization->organization_name) ? $organization->organization_name : ''}}" >
    {!! $errors->first('organization_name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('contact_person_name') ? 'has-error' : ''}}">
    <label for="contact_person_name" class="control-label">{{ 'Contact Person Name' }}</label>
    <input class="form-control" name="contact_person_name" type="text" id="contact_person_name" value="{{ isset($organization->contact_person_name) ? $organization->contact_person_name : ''}}" >
    {!! $errors->first('contact_person_name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('contact_no') ? 'has-error' : ''}}">
    <label for="contact_no" class="control-label">{{ 'Contact No' }}</label>
    <input class="form-control" name="contact_no" type="text" id="contact_no" value="{{ isset($organization->contact_no) ? $organization->contact_no : ''}}" >
    {!! $errors->first('contact_no', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('address') ? 'has-error' : ''}}">
    <label for="address" class="control-label">{{ 'Address' }}</label>
    <input class="form-control" name="address" type="text" id="address" value="{{ isset($organization->address) ? $organization->address : ''}}" >
    {!! $errors->first('address', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('created_by') ? 'has-error' : ''}}">
    <label for="created_by" class="control-label">{{ 'Created By' }}</label>
    <input class="form-control" name="created_by" type="number" id="created_by" value="{{ isset($organization->created_by) ? $organization->created_by : ''}}" >
    {!! $errors->first('created_by', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('updated_by') ? 'has-error' : ''}}">
    <label for="updated_by" class="control-label">{{ 'Updated By' }}</label>
    <input class="form-control" name="updated_by" type="number" id="updated_by" value="{{ isset($organization->updated_by) ? $organization->updated_by : ''}}" >
    {!! $errors->first('updated_by', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>

<div class="form-group {{ $errors->has('user_id') ? 'has-error' : ''}}">
    <label for="user_id" class="control-label">{{ 'User Id' }}</label>
    <input class="form-control" name="user_id" type="number" id="user_id" value="{{ isset($trust->user_id) ? $trust->user_id : ''}}" >
    {!! $errors->first('user_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('trust_id') ? 'has-error' : ''}}">
    <label for="trust_id" class="control-label">{{ 'Trust Id' }}</label>
    <input class="form-control" name="trust_id" type="number" id="trust_id" value="{{ isset($trust->trust_id) ? $trust->trust_id : ''}}" >
    {!! $errors->first('trust_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
    <label for="name" class="control-label">{{ 'Name' }}</label>
    <input class="form-control" name="name" type="text" id="name" value="{{ isset($trust->name) ? $trust->name : ''}}" >
    {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('code') ? 'has-error' : ''}}">
    <label for="code" class="control-label">{{ 'Code' }}</label>
    <input class="form-control" name="code" type="text" id="code" value="{{ isset($trust->code) ? $trust->code : ''}}" >
    {!! $errors->first('code', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('preference_invoide_mathod') ? 'has-error' : ''}}">
    <label for="preference_invoide_mathod" class="control-label">{{ 'Preference Invoide Mathod' }}</label>
    <input class="form-control" name="preference_invoide_mathod" type="text" id="preference_invoide_mathod" value="{{ isset($trust->preference_invoide_mathod) ? $trust->preference_invoide_mathod : ''}}" >
    {!! $errors->first('preference_invoide_mathod', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('email_address') ? 'has-error' : ''}}">
    <label for="email_address" class="control-label">{{ 'Email Address' }}</label>
    <input class="form-control" name="email_address" type="text" id="email_address" value="{{ isset($trust->email_address) ? $trust->email_address : ''}}" >
    {!! $errors->first('email_address', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('address_line_1') ? 'has-error' : ''}}">
    <label for="address_line_1" class="control-label">{{ 'Address Line 1' }}</label>
    <input class="form-control" name="address_line_1" type="text" id="address_line_1" value="{{ isset($trust->address_line_1) ? $trust->address_line_1 : ''}}" >
    {!! $errors->first('address_line_1', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('address_line_2') ? 'has-error' : ''}}">
    <label for="address_line_2" class="control-label">{{ 'Address Line 2' }}</label>
    <input class="form-control" name="address_line_2" type="text" id="address_line_2" value="{{ isset($trust->address_line_2) ? $trust->address_line_2 : ''}}" >
    {!! $errors->first('address_line_2', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('address_line_3') ? 'has-error' : ''}}">
    <label for="address_line_3" class="control-label">{{ 'Address Line 3' }}</label>
    <input class="form-control" name="address_line_3" type="text" id="address_line_3" value="{{ isset($trust->address_line_3) ? $trust->address_line_3 : ''}}" >
    {!! $errors->first('address_line_3', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('city') ? 'has-error' : ''}}">
    <label for="city" class="control-label">{{ 'City' }}</label>
    <input class="form-control" name="city" type="text" id="city" value="{{ isset($trust->city) ? $trust->city : ''}}" >
    {!! $errors->first('city', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('post_code') ? 'has-error' : ''}}">
    <label for="post_code" class="control-label">{{ 'Post Code' }}</label>
    <input class="form-control" name="post_code" type="text" id="post_code" value="{{ isset($trust->post_code) ? $trust->post_code : ''}}" >
    {!! $errors->first('post_code', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('trust_portal_url') ? 'has-error' : ''}}">
    <label for="trust_portal_url" class="control-label">{{ 'Trust Portal Url' }}</label>
    <input class="form-control" name="trust_portal_url" type="text" id="trust_portal_url" value="{{ isset($trust->trust_portal_url) ? $trust->trust_portal_url : ''}}" >
    {!! $errors->first('trust_portal_url', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('portal_email') ? 'has-error' : ''}}">
    <label for="portal_email" class="control-label">{{ 'Portal Email' }}</label>
    <input class="form-control" name="portal_email" type="text" id="portal_email" value="{{ isset($trust->portal_email) ? $trust->portal_email : ''}}" >
    {!! $errors->first('portal_email', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('portal_password') ? 'has-error' : ''}}">
    <label for="portal_password" class="control-label">{{ 'Portal Password' }}</label>
    <input class="form-control" name="portal_password" type="text" id="portal_password" value="{{ isset($trust->portal_password) ? $trust->portal_password : ''}}" >
    {!! $errors->first('portal_password', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('first_name') ? 'has-error' : ''}}">
    <label for="first_name" class="control-label">{{ 'First Name' }}</label>
    <input class="form-control" name="first_name" type="text" id="first_name" value="{{ isset($trust->first_name) ? $trust->first_name : ''}}" >
    {!! $errors->first('first_name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('first_name') ? 'has-error' : ''}}">
    <label for="first_name" class="control-label">{{ 'First Name' }}</label>
    <input class="form-control" name="first_name" type="text" id="first_name" value="{{ isset($trust->first_name) ? $trust->first_name : ''}}" >
    {!! $errors->first('first_name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('last_name') ? 'has-error' : ''}}">
    <label for="last_name" class="control-label">{{ 'Last Name' }}</label>
    <input class="form-control" name="last_name" type="text" id="last_name" value="{{ isset($trust->last_name) ? $trust->last_name : ''}}" >
    {!! $errors->first('last_name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('email_address') ? 'has-error' : ''}}">
    <label for="email_address" class="control-label">{{ 'Email Address' }}</label>
    <input class="form-control" name="email_address" type="text" id="email_address" value="{{ isset($trust->email_address) ? $trust->email_address : ''}}" >
    {!! $errors->first('email_address', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('phone_number') ? 'has-error' : ''}}">
    <label for="phone_number" class="control-label">{{ 'Phone Number' }}</label>
    <input class="form-control" name="phone_number" type="number" id="phone_number" value="{{ isset($trust->phone_number) ? $trust->phone_number : ''}}" >
    {!! $errors->first('phone_number', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('client') ? 'has-error' : ''}}">
    <label for="client" class="control-label">{{ 'Client' }}</label>
    <input class="form-control" name="client" type="text" id="client" value="{{ isset($trust->client) ? $trust->client : ''}}" >
    {!! $errors->first('client', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('department') ? 'has-error' : ''}}">
    <label for="department" class="control-label">{{ 'Department' }}</label>
    <input class="form-control" name="department" type="text" id="department" value="{{ isset($trust->department) ? $trust->department : ''}}" >
    {!! $errors->first('department', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>

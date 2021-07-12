<div class="form-group {{ $errors->has('user_id') ? 'has-error' : ''}}">
    <label for="user_id" class="control-label">{{ 'User Id' }}</label>
    <input class="form-control" name="user_id" type="number" id="user_id" value="{{ isset($signeesdetail->user_id) ? $signeesdetail->user_id : ''}}" >
    {!! $errors->first('user_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('candidate_id') ? 'has-error' : ''}}">
    <label for="candidate_id" class="control-label">{{ 'Candidate Id' }}</label>
    <input class="form-control" name="candidate_id" type="text" id="candidate_id" value="{{ isset($signeesdetail->candidate_id) ? $signeesdetail->candidate_id : ''}}" >
    {!! $errors->first('candidate_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('phone_number') ? 'has-error' : ''}}">
    <label for="phone_number" class="control-label">{{ 'Phone Number' }}</label>
    <input class="form-control" name="phone_number" type="number" id="phone_number" value="{{ isset($signeesdetail->phone_number) ? $signeesdetail->phone_number : ''}}" >
    {!! $errors->first('phone_number', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('mobile_number') ? 'has-error' : ''}}">
    <label for="mobile_number" class="control-label">{{ 'Mobile Number' }}</label>
    <input class="form-control" name="mobile_number" type="number" id="mobile_number" value="{{ isset($signeesdetail->mobile_number) ? $signeesdetail->mobile_number : ''}}" >
    {!! $errors->first('mobile_number', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('address_line_1') ? 'has-error' : ''}}">
    <label for="address_line_1" class="control-label">{{ 'Address Line 1' }}</label>
    <input class="form-control" name="address_line_1" type="text" id="address_line_1" value="{{ isset($signeesdetail->address_line_1) ? $signeesdetail->address_line_1 : ''}}" >
    {!! $errors->first('address_line_1', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('address_line_2') ? 'has-error' : ''}}">
    <label for="address_line_2" class="control-label">{{ 'Address Line 2' }}</label>
    <input class="form-control" name="address_line_2" type="text" id="address_line_2" value="{{ isset($signeesdetail->address_line_2) ? $signeesdetail->address_line_2 : ''}}" >
    {!! $errors->first('address_line_2', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('address_line_3') ? 'has-error' : ''}}">
    <label for="address_line_3" class="control-label">{{ 'Address Line 3' }}</label>
    <input class="form-control" name="address_line_3" type="text" id="address_line_3" value="{{ isset($signeesdetail->address_line_3) ? $signeesdetail->address_line_3 : ''}}" >
    {!! $errors->first('address_line_3', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('city') ? 'has-error' : ''}}">
    <label for="city" class="control-label">{{ 'City' }}</label>
    <input class="form-control" name="city" type="text" id="city" value="{{ isset($signeesdetail->city) ? $signeesdetail->city : ''}}" >
    {!! $errors->first('city', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('post_code') ? 'has-error' : ''}}">
    <label for="post_code" class="control-label">{{ 'Post Code' }}</label>
    <input class="form-control" name="post_code" type="text" id="post_code" value="{{ isset($signeesdetail->post_code) ? $signeesdetail->post_code : ''}}" >
    {!! $errors->first('post_code', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('date_of_birth') ? 'has-error' : ''}}">
    <label for="date_of_birth" class="control-label">{{ 'Date Of Birth' }}</label>
    <input class="form-control" name="date_of_birth" type="date" id="date_of_birth" value="{{ isset($signeesdetail->date_of_birth) ? $signeesdetail->date_of_birth : ''}}" >
    {!! $errors->first('date_of_birth', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('nationality') ? 'has-error' : ''}}">
    <label for="nationality" class="control-label">{{ 'Nationality' }}</label>
    <input class="form-control" name="nationality" type="text" id="nationality" value="{{ isset($signeesdetail->nationality) ? $signeesdetail->nationality : ''}}" >
    {!! $errors->first('nationality', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('candidate_referred_from') ? 'has-error' : ''}}">
    <label for="candidate_referred_from" class="control-label">{{ 'Candidate Referred From' }}</label>
    <input class="form-control" name="candidate_referred_from" type="text" id="candidate_referred_from" value="{{ isset($signeesdetail->candidate_referred_from) ? $signeesdetail->candidate_referred_from : ''}}" >
    {!! $errors->first('candidate_referred_from', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('date_registered') ? 'has-error' : ''}}">
    <label for="date_registered" class="control-label">{{ 'Date Registered' }}</label>
    <input class="form-control" name="date_registered" type="date" id="date_registered" value="{{ isset($signeesdetail->date_registered) ? $signeesdetail->date_registered : ''}}" >
    {!! $errors->first('date_registered', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>

<div class="form-group {{ $errors->has('organization_id') ? 'has-error' : ''}}">
    <label for="organization_id" class="control-label">{{ 'Organization Id' }}</label>
    <input class="form-control" name="organization_id" type="number" id="organization_id" value="{{ isset($bookingmatch->organization_id) ? $bookingmatch->organization_id : ''}}" >
    {!! $errors->first('organization_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('candidate_id') ? 'has-error' : ''}}">
    <label for="candidate_id" class="control-label">{{ 'Candidate Id' }}</label>
    <input class="form-control" name="candidate_id" type="number" id="candidate_id" value="{{ isset($bookingmatch->candidate_id) ? $bookingmatch->candidate_id : ''}}" >
    {!! $errors->first('candidate_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('booking_id') ? 'has-error' : ''}}">
    <label for="booking_id" class="control-label">{{ 'Booking Id' }}</label>
    <input class="form-control" name="booking_id" type="number" id="booking_id" value="{{ isset($bookingmatch->booking_id) ? $bookingmatch->booking_id : ''}}" >
    {!! $errors->first('booking_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('trust_id') ? 'has-error' : ''}}">
    <label for="trust_id" class="control-label">{{ 'Trust Id' }}</label>
    <input class="form-control" name="trust_id" type="number" id="trust_id" value="{{ isset($bookingmatch->trust_id) ? $bookingmatch->trust_id : ''}}" >
    {!! $errors->first('trust_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('match_count') ? 'has-error' : ''}}">
    <label for="match_count" class="control-label">{{ 'Match Count' }}</label>
    <input class="form-control" name="match_count" type="number" id="match_count" value="{{ isset($bookingmatch->match_count) ? $bookingmatch->match_count : ''}}" >
    {!! $errors->first('match_count', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('booking_date') ? 'has-error' : ''}}">
    <label for="booking_date" class="control-label">{{ 'Booking Date' }}</label>
    <input class="form-control" name="booking_date" type="date" id="booking_date" value="{{ isset($bookingmatch->booking_date) ? $bookingmatch->booking_date : ''}}" >
    {!! $errors->first('booking_date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('booking_status') ? 'has-error' : ''}}">
    <label for="booking_status" class="control-label">{{ 'Booking Status' }}</label>
    <input class="form-control" name="booking_status" type="text" id="booking_status" value="{{ isset($bookingmatch->booking_status) ? $bookingmatch->booking_status : ''}}" >
    {!! $errors->first('booking_status', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('shift_id') ? 'has-error' : ''}}">
    <label for="shift_id" class="control-label">{{ 'Shift Id' }}</label>
    <input class="form-control" name="shift_id" type="number" id="shift_id" value="{{ isset($bookingmatch->shift_id) ? $bookingmatch->shift_id : ''}}" >
    {!! $errors->first('shift_id', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>

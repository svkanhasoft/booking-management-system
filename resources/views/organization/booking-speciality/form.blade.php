<div class="form-group {{ $errors->has('booking_id') ? 'has-error' : ''}}">
    <label for="booking_id" class="control-label">{{ 'Booking Id' }}</label>
    <input class="form-control" name="booking_id" type="number" id="booking_id" value="{{ isset($bookingspeciality->booking_id) ? $bookingspeciality->booking_id : ''}}" >
    {!! $errors->first('booking_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('speciality_id') ? 'has-error' : ''}}">
    <label for="speciality_id" class="control-label">{{ 'Speciality Id' }}</label>
    <input class="form-control" name="speciality_id" type="number" id="speciality_id" value="{{ isset($bookingspeciality->speciality_id) ? $bookingspeciality->speciality_id : ''}}" >
    {!! $errors->first('speciality_id', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>

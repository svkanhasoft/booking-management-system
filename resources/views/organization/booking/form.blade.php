<div class="form-group {{ $errors->has('user_id') ? 'has-error' : ''}}">
    <label for="user_id" class="control-label">{{ 'User Id' }}</label>
    <input class="form-control" name="user_id" type="number" id="user_id" value="{{ isset($booking->user_id) ? $booking->user_id : ''}}" >
    {!! $errors->first('user_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('reference_id') ? 'has-error' : ''}}">
    <label for="reference_id" class="control-label">{{ 'Reference Id' }}</label>
    <input class="form-control" name="reference_id" type="number" id="reference_id" value="{{ isset($booking->reference_id) ? $booking->reference_id : ''}}" >
    {!! $errors->first('reference_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('trust_id') ? 'has-error' : ''}}">
    <label for="trust_id" class="control-label">{{ 'Trust Id' }}</label>
    <input class="form-control" name="trust_id" type="number" id="trust_id" value="{{ isset($booking->trust_id) ? $booking->trust_id : ''}}" >
    {!! $errors->first('trust_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('ward_id') ? 'has-error' : ''}}">
    <label for="ward_id" class="control-label">{{ 'Ward Id' }}</label>
    <input class="form-control" name="ward_id" type="number" id="ward_id" value="{{ isset($booking->ward_id) ? $booking->ward_id : ''}}" >
    {!! $errors->first('ward_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('shift_id') ? 'has-error' : ''}}">
    <label for="shift_id" class="control-label">{{ 'Shift Id' }}</label>
    <input class="form-control" name="shift_id" type="number" id="shift_id" value="{{ isset($booking->shift_id) ? $booking->shift_id : ''}}" >
    {!! $errors->first('shift_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('date') ? 'has-error' : ''}}">
    <label for="date" class="control-label">{{ 'Date' }}</label>
    <input class="form-control" name="date" type="date" id="date" value="{{ isset($booking->date) ? $booking->date : ''}}" >
    {!! $errors->first('date', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('grade_id') ? 'has-error' : ''}}">
    <label for="grade_id" class="control-label">{{ 'Grade Id' }}</label>
    <input class="form-control" name="grade_id" type="number" id="grade_id" value="{{ isset($booking->grade_id) ? $booking->grade_id : ''}}" >
    {!! $errors->first('grade_id', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>

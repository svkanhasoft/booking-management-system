<div class="form-group {{ $errors->has('trust_id') ? 'has-error' : ''}}">
    <label for="trust_id" class="control-label">{{ 'Trust Id' }}</label>
    <input class="form-control" name="trust_id" type="number" id="trust_id" value="{{ isset($ward->trust_id) ? $ward->trust_id : ''}}" >
    {!! $errors->first('trust_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('ward_name') ? 'has-error' : ''}}">
    <label for="ward_name" class="control-label">{{ 'Ward Name' }}</label>
    <input class="form-control" name="ward_name" type="text" id="ward_name" value="{{ isset($ward->ward_name) ? $ward->ward_name : ''}}" >
    {!! $errors->first('ward_name', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('ward_type') ? 'has-error' : ''}}">
    <label for="ward_type" class="control-label">{{ 'Ward Type' }}</label>
    <input class="form-control" name="ward_type" type="text" id="ward_type" value="{{ isset($ward->ward_type) ? $ward->ward_type : ''}}" >
    {!! $errors->first('ward_type', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('ward_number') ? 'has-error' : ''}}">
    <label for="ward_number" class="control-label">{{ 'Ward Number' }}</label>
    <input class="form-control" name="ward_number" type="number" id="ward_number" value="{{ isset($ward->ward_number) ? $ward->ward_number : ''}}" >
    {!! $errors->first('ward_number', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>

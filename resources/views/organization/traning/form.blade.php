<div class="form-group {{ $errors->has('trust_id') ? 'has-error' : ''}}">
    <label for="trust_id" class="control-label">{{ 'Trust Id' }}</label>
    <input class="form-control" name="trust_id" type="number" id="trust_id" value="{{ isset($traning->trust_id) ? $traning->trust_id : ''}}" >
    {!! $errors->first('trust_id', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group {{ $errors->has('traning_name') ? 'has-error' : ''}}">
    <label for="traning_name" class="control-label">{{ 'Traning Name' }}</label>
    <input class="form-control" name="traning_name" type="text" id="traning_name" value="{{ isset($traning->traning_name) ? $traning->traning_name : ''}}" >
    {!! $errors->first('traning_name', '<p class="help-block">:message</p>') !!}
</div>


<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>

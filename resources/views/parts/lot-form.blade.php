@include('parts/lot-form-item', ['field'=>'currency_name', 'caption'=>'Currency Name'])
@include('parts/lot-form-item', ['field'=>'price', 'caption'=>'Price'])

<div class="form-group">
    <label for="time_close">Time before closed</label>
    {!! Form::time('time_close',old('time_close'),array('class' => 'form-control')) !!}
    @if ($errors->has('time_close'))
    <span class="help-block">{{ $errors->first('time_close') }}</span>
    @endif
</div>


<button type="submit" class="btn btn-primary">Save</button
<div class="form-group">
    <label for="{{ $field }}">{{ $caption }}</label>
    <input type="text" class="form-control" name="{{ $field }}" 
        placeholder="{{ $caption }}" 
        value="{{ old($field,  isset($lot) ? $lot[$field] : null) }}">
    @if ($errors->has($field))
    <span class="help-block">{{ $errors->first($field) }}</span>
    @endif
</div>
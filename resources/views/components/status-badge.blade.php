@props(['value'])

@if ($value)
    <span class="{{ is_object($value) && method_exists($value, 'badge') ? $value->badge() : 'badge badge-gray' }}">{{ is_object($value) && method_exists($value, 'label') ? $value->label() : $value }}</span>
@endif

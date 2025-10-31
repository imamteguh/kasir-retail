@props([
    'label' => null,
    'name',
    'checked' => false,
    'wrapperClass' => '',
])

<div class="form-check form-switch {{ $wrapperClass }}">
    <input
        type="checkbox"
        name="{{ $name }}"
        id="{{ $name }}"
        value="1"
        {{ old($name, $checked) ? 'checked' : '' }}
        {{ $attributes->class(['form-check-input']) }}
    >
    @if ($label)
        <label class="form-check-label switch-label" for="{{ $name }}">
            {{ $label }}
        </label>
    @endif
</div>

@props([
    'label' => null,
    'name',
    'type' => 'text',
    'placeholder' => '',
    'value' => old($name),
    'wrapperClass' => '',
])

<div {{ $attributes->merge(['class' => 'form-control-validation ' . $wrapperClass]) }}>
    @if($label)
        <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : '')]) }}
    />

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
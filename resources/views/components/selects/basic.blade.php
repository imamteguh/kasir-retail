@props([
    'label' => null,
    'name',
    'options' => [],
    'selected' => old($name),
    'placeholder' => null,
    'wrapperClass' => '',
])

<div class="form-control-validation {{ $wrapperClass }}">
    @if ($label)
        <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $attributes->class([
            'form-select',
            'is-invalid' => $errors->has($name),
        ]) }}
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        {{-- Jika ada slot manual --}}
        {{ $slot }}

        {{-- Jika tidak ada slot, render dari $options --}}
        @unless($slot->isNotEmpty())
            @foreach ($options as $key => $value)
                <option value="{{ $key }}" {{ (string) $selected === (string) $key ? 'selected' : '' }}>
                    {{ $value }}
                </option>
            @endforeach
        @endunless
    </select>

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@props([
    'label' => null,
    'name',
    'type' => 'text',
    'placeholder' => '',
    'value' => old($name),
    'wrapperClass' => '',
    'append' => null, // konten di kanan input (icon / text)
    'prepend' => null, // konten di kiri input
])

<div class="mb-6 form-control-validation {{ $wrapperClass }}">
    @if ($label)
        <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    @endif

    <div class="input-group input-group-merge">
        {{-- prepend --}}
        @if ($prepend)
            <span class="input-group-text">{!! $prepend !!}</span>
        @endif

        {{-- input utama --}}
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->class([
                'form-control',
                'is-invalid' => $errors->has($name),
            ]) }}
        />

        {{-- append --}}
        @if ($append)
            <span class="input-group-text cursor-pointer">{!! $append !!}</span>
        @endif
    </div>

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

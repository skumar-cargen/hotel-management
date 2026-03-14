@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'required' => false,
    'placeholder' => '-- Select --',
    'help' => null,
])

<div {{ $attributes->only('class')->merge(['class' => '']) }}>
    @if($label)
    <label for="{{ $name }}" class="form-label">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
    @endif
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $required ? 'required' : '' }}
        class="form-select select2 {{ $errors->has($name) ? 'is-invalid' : '' }}"
    >
        @if($placeholder)
        <option value="">{{ $placeholder }}</option>
        @endif
        @foreach($options as $optValue => $optLabel)
        <option value="{{ $optValue }}" {{ (string) old($name, $selected) === (string) $optValue ? 'selected' : '' }}>{{ $optLabel }}</option>
        @endforeach
    </select>
    @if($help)
    <small class="text-muted">{{ $help }}</small>
    @endif
    @error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

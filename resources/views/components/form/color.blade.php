@props([
    'name',
    'label' => null,
    'value' => '#000000',
    'help' => null,
])

<div {{ $attributes->only('class')->merge(['class' => '']) }}>
    @if($label)
    <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    @endif
    <div class="d-flex gap-2 align-items-center">
        <input
            type="color"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, $value) }}"
            class="form-control form-control-color"
        >
        <input
            type="text"
            class="form-control form-control-sm"
            value="{{ old($name, $value) }}"
            style="width:80px"
            readonly
            data-color-text="{{ $name }}"
        >
    </div>
    @if($help)
    <small class="text-muted">{{ $help }}</small>
    @endif
    @error($name)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>

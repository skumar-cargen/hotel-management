@props([
    'name',
    'label' => null,
    'checked' => false,
    'value' => '1',
    'help' => null,
])

<div class="form-check {{ $attributes->get('class', '') }}">
    <input
        type="checkbox"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ $value }}"
        class="form-check-input {{ $errors->has($name) ? 'is-invalid' : '' }}"
        {{ old($name, $checked) ? 'checked' : '' }}
    >
    @if($label)
    <label class="form-check-label" for="{{ $name }}">{{ $label }}</label>
    @endif
    @if($help)
    <small class="text-muted d-block">{{ $help }}</small>
    @endif
    @error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

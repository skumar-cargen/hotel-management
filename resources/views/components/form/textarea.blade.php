@props([
    'name',
    'label' => null,
    'value' => null,
    'required' => false,
    'placeholder' => '',
    'rows' => 3,
    'help' => null,
])

<div {{ $attributes->only('class')->merge(['class' => '']) }}>
    @if($label)
    <label for="{{ $name }}" class="form-label">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
    @endif
    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        class="form-control {{ $errors->has($name) ? 'is-invalid' : '' }}"
    >{{ old($name, $value) }}</textarea>
    @if($help)
    <small class="text-muted">{{ $help }}</small>
    @endif
    @error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

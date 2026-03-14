@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'required' => false,
    'placeholder' => '',
    'help' => null,
    'prepend' => null,
    'append' => null,
])

<div {{ $attributes->only('class')->merge(['class' => '']) }}>
    @if($label)
    <label for="{{ $name }}" class="form-label">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
    @endif
    @if($prepend || $append)
    <div class="input-group">
        @if($prepend)<span class="input-group-text">{!! $prepend !!}</span>@endif
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->except('class')->merge(['class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : '')]) }}
        >
        @if($append)<span class="input-group-text">{!! $append !!}</span>@endif
    </div>
    @else
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->except('class')->merge(['class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : '')]) }}
    >
    @endif
    @if($help)
    <small class="text-muted">{{ $help }}</small>
    @endif
    @error($name)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>

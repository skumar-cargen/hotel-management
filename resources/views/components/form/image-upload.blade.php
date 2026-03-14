@props([
    'name',
    'label' => null,
    'currentImage' => null,
    'required' => false,
    'multiple' => false,
    'accept' => 'image/*',
    'help' => null,
])

<div {{ $attributes->only('class')->merge(['class' => '']) }}>
    @if($label)
    <label for="{{ $name }}" class="form-label">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
    @endif

    @if($currentImage)
    <div class="mb-2">
        <img src="{{ asset('storage/' . $currentImage) }}" alt="Current image" class="rounded" style="max-height: 100px;">
    </div>
    @endif

    <input
        type="file"
        name="{{ $name }}{{ $multiple ? '[]' : '' }}"
        id="{{ $name }}"
        accept="{{ $accept }}"
        {{ $required ? 'required' : '' }}
        {{ $multiple ? 'multiple' : '' }}
        class="form-control {{ $errors->has($name) ? 'is-invalid' : '' }}"
    >
    @if($help)
    <small class="text-muted">{{ $help }}</small>
    @endif
    @error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

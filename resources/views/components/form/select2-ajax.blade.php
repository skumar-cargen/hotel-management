@props([
    'name',
    'label' => null,
    'url',
    'selected' => null,
    'selectedText' => null,
    'required' => false,
    'placeholder' => 'Search...',
    'multiple' => false,
    'help' => null,
])

<div {{ $attributes->only('class')->merge(['class' => '']) }}>
    @if($label)
    <label for="{{ $name }}" class="form-label">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
    @endif
    <select
        name="{{ $name }}{{ $multiple ? '[]' : '' }}"
        id="select2-{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $multiple ? 'multiple' : '' }}
        class="form-select {{ $errors->has($name) ? 'is-invalid' : '' }}"
        data-ajax-url="{{ $url }}"
        data-placeholder="{{ $placeholder }}"
    >
        @if($selected && $selectedText)
        <option value="{{ $selected }}" selected>{{ $selectedText }}</option>
        @endif
    </select>
    @if($help)
    <small class="text-muted">{{ $help }}</small>
    @endif
    @error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

@once
@push('select2-ajax-init')
<script>
$(document).ready(function() {
    $('[data-ajax-url]').each(function() {
        var $el = $(this);
        $el.select2({
            theme: 'bootstrap-5',
            ajax: {
                url: $el.data('ajax-url'),
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return { q: params.term, page: params.page || 1 };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: { more: data.has_more || false }
                    };
                },
                cache: true
            },
            placeholder: $el.data('placeholder'),
            allowClear: !$el.prop('required'),
            minimumInputLength: 1,
        });
    });
});
</script>
@endpush
@endonce

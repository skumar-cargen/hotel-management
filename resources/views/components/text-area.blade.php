<div class="form-group text-area-div" style="{{ $divStyle }}">
    @php
        $extraLabelClass = $extraLabelClass ?? ''; // Ensures $extraLabelClass has a default value if not set
    @endphp
    <label {{ $attributes->merge(['class' => 'block font-medium font-weight-bold text-sm text-gray-700 '.$extraLabelClass]) }}>
        {{ $placeholder}}
        @if($required)
            <span class="text-danger">*</span>
        @endif
    </label>
    {{-- Safe: browsers don't parse HTML inside textarea elements --}}
    <textarea class="form-control mb-3 tinymceExample" {{ $required ? 'required' : '' }} name="{{ $name }}" id="tinymceExample" rows="{{ $rows }}" {{ $required ? "required" : '' }}>{!! $value !!}</textarea>
    @error($name)
        <div class="text text-danger">{{ $message }}</div>
    @enderror
    <div class="error-message text text-danger"></div>
</div>

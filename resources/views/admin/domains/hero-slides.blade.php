<x-admin-layout title="Domain Slides" :pageTitle="'Domain Slides: ' . $domain->name">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item"><a href="{{ route('admin.domains.index') }}">Domains</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.domains.edit', $domain) }}">{{ $domain->name }}</a></li>
        <li class="breadcrumb-item active">Domain Slides</li>
    </x-slot:breadcrumb>

    @include('admin.domains._hero-slides-tab')
</x-admin-layout>

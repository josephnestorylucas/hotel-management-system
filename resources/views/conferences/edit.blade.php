{{-- resources/views/conferences/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit — ' . $conference->title)
@section('page-title', 'Conferences')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Edit Conference</h2>
        </div>
        <form method="POST" action="{{ route('conferences.update', $conference) }}" class="p-6">
            @csrf
            @method('PUT')
            <div class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $conference->title) }}" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description', $conference->description) }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="conference_fee" class="block text-sm font-medium text-gray-700 mb-2">Conference Fee (TZS) *</label>
                        <input type="number" name="conference_fee" id="conference_fee" value="{{ old('conference_fee', $conference->conference_fee) }}" min="0" step="0.01" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="hall_name" class="block text-sm font-medium text-gray-700 mb-2">Venue Name</label>
                        <input type="text" name="hall_name" id="hall_name" value="{{ old('hall_name', $conference->hall_name) }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="start_datetime" class="block text-sm font-medium text-gray-700 mb-2">Start *</label>
                        <input type="datetime-local" name="start_datetime" id="start_datetime"
                            value="{{ old('start_datetime', $conference->start_datetime->format('Y-m-d\TH:i')) }}" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="end_datetime" class="block text-sm font-medium text-gray-700 mb-2">End *</label>
                        <input type="datetime-local" name="end_datetime" id="end_datetime"
                            value="{{ old('end_datetime', $conference->end_datetime->format('Y-m-d\TH:i')) }}" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" id="status" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @foreach(['draft','scheduled','ongoing','completed','cancelled'] as $s)
                        <option value="{{ $s }}" {{ old('status', $conference->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('conferences.show', $conference) }}" class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Update Conference</button>
            </div>
        </form>
    </div>
</div>
@endsection

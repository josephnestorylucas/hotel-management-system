# Spatie Laravel Media Library Setup

This document explains how to set up and use the Spatie Laravel Media Library integration for guest photos and ID documents in the Hotel Management System.

## Prerequisites

The package is already installed. If you're setting up a fresh environment, run:

```bash
composer require spatie/laravel-medialibrary
```

## Database Setup

### 1. Publish and Run Migration

The media library requires a `media` table in your database:

```bash
# Publish the migration file
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"

# Run migrations
php artisan migrate
```

### 2. Create Storage Link

Ensure the storage link exists for serving files:

```bash
php artisan storage:link
```

## Configuration

### 1. Publish Configuration (Optional)

If you need to customize the media library settings:

```bash
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-config"
```

This creates `config/media-library.php` where you can adjust:
- Default disk
- Maximum file size
- Queue settings for conversions
- Custom path generators

### 2. Environment Variables

Add these to your `.env` file if needed:

```env
# Filesystem disk for media (default: public)
MEDIA_DISK=public

# Queue media conversions (recommended for production)
QUEUE_MEDIA_CONVERSIONS=true
```

## Implementation Details

### Guest Model Media Collections

The `Guest` model has two media collections defined:

#### 1. `guest_photo` Collection
- **Purpose**: Store profile photo for the guest
- **Single File**: Yes (replaces existing photo when new one is uploaded)
- **Accepted MIME Types**: `image/jpeg`, `image/png`, `image/jpg`
- **Conversions**: 
  - `thumb` (150x150 pixels)
  - `medium` (400x400 pixels)

#### 2. `id_documents` Collection
- **Purpose**: Store ID documents (passport, driver's license, etc.)
- **Multiple Files**: Yes
- **Accepted MIME Types**: `image/jpeg`, `image/png`, `image/jpg`, `application/pdf`
- **Conversions**: 
  - `thumb` (150x150 pixels) - only for images

### Model Methods

The `Guest` model provides these helper methods:

```php
// Check if guest has a photo
$guest->hasPhoto();  // Returns boolean

// Get photo URLs
$guest->photo_url;        // Original photo URL
$guest->photo_thumb_url;  // Thumbnail URL (150x150)
$guest->photo_medium_url; // Medium size URL (400x400)

// Check if guest has ID documents
$guest->hasIdDocuments();  // Returns boolean

// Get ID documents collection
$guest->id_documents;  // Returns Collection of media items

// Get document count
$guest->id_documents_count;  // Returns integer

// Get first ID document URL
$guest->id_document_url;  // URL of first document
```

## Usage Examples

### Uploading a Photo

```php
// In controller
if ($request->hasFile('photo')) {
    $guest->addMediaFromRequest('photo')
          ->toMediaCollection('guest_photo');
}
```

### Uploading Multiple ID Documents

```php
// In controller
if ($request->hasFile('id_documents')) {
    foreach ($request->file('id_documents') as $document) {
        $guest->addMedia($document)
              ->toMediaCollection('id_documents');
    }
}
```

### Replacing a Photo

The `guest_photo` collection is configured with `singleFile()`, so uploading a new photo automatically replaces the old one:

```php
// This will replace any existing photo
$guest->addMediaFromRequest('photo')
      ->toMediaCollection('guest_photo');
```

### Removing a Photo

```php
// Remove all photos from the collection
$guest->clearMediaCollection('guest_photo');
```

### Removing Specific Documents

```php
// Remove specific documents by ID
foreach ($documentIds as $mediaId) {
    $guest->media()->where('id', $mediaId)->delete();
}

// Or via the route
// DELETE /guests/{guest}/media/{media}
```

### Displaying in Views

```blade
{{-- Profile Photo --}}
@if($guest->hasPhoto())
    <img src="{{ $guest->photo_thumb_url }}" alt="{{ $guest->full_name }}">
@else
    <div class="avatar-placeholder">{{ $guest->initials }}</div>
@endif

{{-- ID Documents --}}
@if($guest->hasIdDocuments())
    @foreach($guest->id_documents as $document)
        <div class="document">
            @if($document->mime_type === 'application/pdf')
                <a href="{{ $document->getUrl() }}" target="_blank">
                    {{ $document->file_name }}
                </a>
            @else
                <img src="{{ $document->getUrl('thumb') }}" alt="{{ $document->file_name }}">
            @endif
            <span>{{ number_format($document->size / 1024, 1) }} KB</span>
        </div>
    @endforeach
@endif
```

## Form Setup

### HTML Form Requirements

Forms that upload files must include:
1. `enctype="multipart/form-data"` attribute
2. Proper input names:
   - `name="photo"` for guest photo
   - `name="id_documents[]"` for multiple ID documents (with `multiple` attribute)

```html
<form method="POST" action="{{ route('guests.store') }}" enctype="multipart/form-data">
    @csrf
    
    <!-- Single photo upload -->
    <input type="file" name="photo" accept=".jpg,.jpeg,.png">
    
    <!-- Multiple documents upload -->
    <input type="file" name="id_documents[]" multiple accept=".jpg,.jpeg,.png,.pdf">
    
    <button type="submit">Save</button>
</form>
```

### Edit Form: Removing Existing Files

For the edit form, use checkboxes to mark files for removal:

```html
<!-- Remove photo checkbox -->
@if($guest->hasPhoto())
    <div>
        <img src="{{ $guest->photo_thumb_url }}" alt="Current photo">
        <label>
            <input type="checkbox" name="remove_photo" value="1">
            Remove photo
        </label>
    </div>
@endif

<!-- Remove specific documents -->
@foreach($guest->id_documents as $document)
    <div>
        <span>{{ $document->file_name }}</span>
        <label>
            <input type="checkbox" name="remove_documents[]" value="{{ $document->id }}">
            Remove
        </label>
    </div>
@endforeach
```

## Validation Rules

### Controller Validation

```php
// For store/create
$request->validate([
    'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    'id_documents.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
]);

// For update
$request->validate([
    'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    'id_documents.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
    'remove_photo' => 'nullable|boolean',
    'remove_documents' => 'nullable|array',
    'remove_documents.*' => 'exists:media,id',
]);
```

## File Storage Location

By default, files are stored in:
```
storage/app/public/{model_id}/{collection_name}/{file}
```

Conversions are stored in:
```
storage/app/public/{model_id}/conversions/{file}
```

Files are accessible via:
```
/storage/{model_id}/{collection_name}/{file}
```

## Queue Configuration (Production)

For production, it's recommended to queue media conversions:

### 1. Update Configuration

In `config/media-library.php`:

```php
'queue_conversions_by_default' => env('QUEUE_MEDIA_CONVERSIONS', true),
```

### 2. Run Queue Worker

```bash
php artisan queue:work
```

### 3. Or Use Supervisor

Create a supervisor configuration to keep the queue worker running:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/storage/logs/worker.log
```

## Troubleshooting

### Images Not Displaying

1. Check storage link: `php artisan storage:link`
2. Verify file permissions on `storage/app/public`
3. Check if the file exists: `$media->getPath()`

### Conversions Not Generated

1. For queued conversions, ensure queue worker is running
2. Check if GD or Imagick is installed: `php -m | grep -E 'gd|imagick'`
3. For non-queued conversions, check disk space

### 413 Request Entity Too Large

Increase upload limits in:
- `php.ini`: `upload_max_filesize`, `post_max_size`
- Nginx: `client_max_body_size`
- Apache: `LimitRequestBody`

### File Upload Validation Failing

Ensure your form has `enctype="multipart/form-data"` attribute.

## API Reference

For more advanced usage, refer to the official Spatie Media Library documentation:
https://spatie.be/docs/laravel-medialibrary/v11/introduction

<?php

namespace Tests\Feature\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait InteractsWithStorage
{
    /**
     * Create a fake file with the given name and dimensions.
     */
    public function createFakeImage(string $name, int $width = 1200, int $height = 800): UploadedFile
    {
        return UploadedFile::fake()->image($name, $width, $height);
    }

    /**
     * Assert that a file exists in the storage.
     */
    public function assertStorageHas(string $path): void
    {
        $this->assertTrue(
            Storage::disk('s3')->exists($path),
            "Failed asserting that file exists: {$path}"
        );
    }

    /**
     * Assert that a file does not exist in the storage.
     */
    public function assertStorageMissing(string $path): void
    {
        $this->assertFalse(
            Storage::disk('s3')->exists($path),
            "Failed asserting that file does not exist: {$path}"
        );
    }

    /**
     * Clean up the storage after tests.
     */
    public function cleanStorage(): void
    {
        Storage::disk('s3')->deleteDirectory('');
    }
}

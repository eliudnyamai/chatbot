<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use OpenAI\Laravel\Facades\OpenAI;

class UploadDocs extends Command
{
    protected $signature = 'upload-docs';

    protected $description = 'Uploads the docs to OpenAI';

    public function handle()
    {
        $uploadedFile = OpenAI::files()->upload([
            'file' => Storage::disk('public')->readStream('training_data.json'),
            'purpose' => 'assistants',
        ]);

        $this->info('File ID: '.$uploadedFile->id);
    }
}
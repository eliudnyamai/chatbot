<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TrainingData;
use Illuminate\Support\Facades\Storage;

class PrepareDocs extends Command
{
    protected $signature = 'app:prepare-docs';

    protected $description = 'Create a file containing all our training data';

    public function handle()
    {
        // Retrieve all training data
        $trainingData = TrainingData::all();

        // Convert data to JSON
        $jsonData = $trainingData->toJson(JSON_PRETTY_PRINT);

        // Define the export path
        $exportPath = 'public/training_data.json';

        // Store the JSON data
        Storage::put($exportPath, $jsonData);

        // Return path to the saved JSON file
        $this->info('Training data exported successfully. File saved at: ' . storage_path($exportPath));
    }
}

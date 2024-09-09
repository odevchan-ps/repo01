<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NewsImportService;

class FetchNewsToDB extends Command
{
    protected $signature = 'fetch:news';
    protected $description = 'Fetch RSS news and store them into the database';

    protected $newsImportService;

    public function __construct(NewsImportService $newsImportService)
    {
        parent::__construct();
        $this->newsImportService = $newsImportService;
    }

    public function handle()
    {
        $success = $this->newsImportService->importNews('nhk_auto_fetch');

        if ($success) {
            $this->info('News articles fetched and stored successfully.');
        } else {
            $this->error('Error during news fetch.');
        }
    }
}

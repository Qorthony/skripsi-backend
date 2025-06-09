<?php

namespace App\Console\Commands;

use App\Jobs\SendEmailJob;
use Illuminate\Console\Command;

class TestQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test queue functionality by dispatching sample jobs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Dispatching test jobs to queue...');

        // Dispatch beberapa job untuk testing
        for ($i = 1; $i <= 5; $i++) {
            SendEmailJob::dispatch(
                "test{$i}@example.com",
                "Test Subject {$i}",
                "This is test message {$i}"
            );
            
            $this->info("Job {$i} dispatched");
        }

        $this->info('All test jobs dispatched successfully!');
        $this->info('Check logs to see queue processing...');
    }
}

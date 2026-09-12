<?php

namespace App\Console\Commands;

use App\Models\ClickLog;
use App\Models\PhishingLogs;
use Illuminate\Console\Command;

class PurgeOldPhishingLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * --days lets you override the retention period when running it
     * manually, e.g. `php artisan phishing:purge --days=30`.
     */
    protected $signature = 'phishing:purge {--days=90 : Delete logs older than this many days}';

    /**
     * The console command description.
     */
    protected $description = 'Delete captured phishing credentials and click logs older than the retention period';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $deletedLogs = PhishingLogs::where('created_at', '<', $cutoff)->delete();
        $deletedClicks = ClickLog::where('created_at', '<', $cutoff)->delete();

        $this->info("Deleted {$deletedLogs} phishing log(s) and {$deletedClicks} click log(s) older than {$days} days.");

        return self::SUCCESS;
    }
}

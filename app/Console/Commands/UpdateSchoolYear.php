<?php

namespace App\Console\Commands;

use App\Services\SchoolYearUpdateService;
use Illuminate\Console\Command;

class UpdateSchoolYear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'school-year:update {school_year} {--old= : Previous school year to update from}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update staff and students to a new school year';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $newSchoolYear = $this->argument('school_year');
        $oldSchoolYear = $this->option('old');

        $this->info("Updating school year to: {$newSchoolYear}");
        if ($oldSchoolYear) {
            $this->info("From previous school year: {$oldSchoolYear}");
        }

        $results = SchoolYearUpdateService::updateSchoolYear($newSchoolYear, $oldSchoolYear);

        $this->info("✅ Staff updated: {$results['staff_updated']}");
        $this->info("✅ Students updated: {$results['students_updated']}");

        if (!empty($results['errors'])) {
            $this->error("❌ Errors occurred:");
            foreach ($results['errors'] as $error) {
                $this->error("  - {$error}");
            }
            return 1;
        }

        $this->info("🎉 School year update completed successfully!");
        return 0;
    }
}

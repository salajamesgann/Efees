<?php

use App\Models\Discount;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('discounts')) {
            return;
        }

        // 2nd Child Discount (e.g. 10%)
        Discount::firstOrCreate(
            ['discount_name' => 'Sibling Discount - 2nd Child'],
            [
                'type' => 'percentage',
                'value' => 10.00, // Default 10%
                'description' => 'Automatic discount for the second child enrolled.',
                'is_automatic' => true,
                'is_active' => true,
                'priority' => 10,
                'eligibility_rules' => [
                    [
                        'field' => 'sibling_rank',
                        'value' => 2,
                    ],
                ],
                'applicable_grades' => null, // All grades
            ]
        );

        // 3rd Child Discount (e.g. 20%)
        Discount::firstOrCreate(
            ['discount_name' => 'Sibling Discount - 3rd Child'],
            [
                'type' => 'percentage',
                'value' => 20.00, // Default 20%
                'description' => 'Automatic discount for the third child enrolled.',
                'is_automatic' => true,
                'is_active' => true,
                'priority' => 11, // Higher priority? Or same? Usually doesn't matter unless exclusive.
                'eligibility_rules' => [
                    [
                        'field' => 'sibling_rank',
                        'value' => 3,
                    ],
                ],
                'applicable_grades' => null,
            ]
        );

        // 4th Child Discount (Optional - mimicking 3rd child logic for 4th+)
        // User only asked for 2nd and 3rd explicitly, but often it continues.
        // I will stick to requirements: 2nd -> X%, 3rd -> Y%.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Discount::where('discount_name', 'Sibling Discount - 2nd Child')->delete();
        Discount::where('discount_name', 'Sibling Discount - 3rd Child')->delete();
    }
};

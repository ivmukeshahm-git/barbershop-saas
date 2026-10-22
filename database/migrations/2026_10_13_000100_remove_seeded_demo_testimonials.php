<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $demoReviews = [
        'Best fade I\'ve had in years. Clean shop, amazing attention to detail, and the booking system is so easy.',
        'Luxury vibe without being pretentious. Hot towel shave was incredible — I\'ll be back every month.',
        'Friendly barbers who really listen. My beard has never looked this sharp. Highly recommend.',
    ];

    public function up(): void
    {
        DB::table('tbl_testimonial')
            ->whereIn('review', $this->demoReviews)
            ->whereIn('client_name', ['James Carter', 'Michael Reyes', 'David Nguyen'])
            ->update(['status' => 0, 'deleted_at' => now(), 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('tbl_testimonial')
            ->whereIn('review', $this->demoReviews)
            ->whereIn('client_name', ['James Carter', 'Michael Reyes', 'David Nguyen'])
            ->update(['status' => 1, 'deleted_at' => null, 'updated_at' => now()]);
    }
};

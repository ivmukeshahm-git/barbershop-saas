<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('tbl_appointment')
            ->select(['appointment_id', 'booking_code'])
            ->whereNotNull('booking_code')
            ->orderBy('appointment_id')
            ->chunkById(500, function ($appointments) {
                foreach ($appointments as $appointment) {
                    if (! preg_match('/^BCB-\d{8}-/i', $appointment->booking_code)) {
                        continue;
                    }

                    for ($attempt = 0; $attempt < 25; $attempt++) {
                        $code = 'BCB-'.strtoupper(Str::random(32));
                        $exists = DB::table('tbl_appointment')
                            ->where('booking_code', $code)
                            ->exists();

                        if (! $exists) {
                            DB::table('tbl_appointment')
                                ->where('appointment_id', $appointment->appointment_id)
                                ->update(['booking_code' => $code]);

                            continue 2;
                        }
                    }

                    throw new RuntimeException('Unable to generate a unique booking code while migrating appointments.');
                }
            }, 'appointment_id');
    }

    public function down(): void
    {
        // Previous booking codes are not recoverable after rotation.
    }
};

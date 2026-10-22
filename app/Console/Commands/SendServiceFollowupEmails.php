<?php

namespace App\Console\Commands;


use App\Models\Service;
use App\Mail\ServiceFollowupMail;
use App\Models\Setting;
use App\Models\Appointment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendServiceFollowupEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:service-followups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send follow-up emails 15 days after service date';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
                $setting = Setting::where('id', 1)->first();
        $reminderDays = $setting->service_reminder_day ?? 1;

        // Calculate date based on service_reminder_day
      $reminderDate = $reminderDays == 0 ? Carbon::today() : Carbon::today()->subDays($reminderDays);

        $appointments = DB::table('tbl_appointment as a')
            ->select(
                'a.appointment_id',
                'a.date',
                'a.customer_id',
                'c.customer_name',
                'c.customer_email',
                DB::raw("GROUP_CONCAT(d.service_name ORDER BY d.service_name SEPARATOR ', ') as service_names")
            )
            ->join('tbl_appointment_service as b', 'a.appointment_id', '=', 'b.appointment_id')
            ->join('customers as c', 'c.customer_id', '=', 'a.customer_id')
            ->join('tbl_service as d', 'b.service_id', '=', 'd.id')
            ->whereDate('a.date', $reminderDate)
            ->groupBy('a.appointment_id', 'a.date', 'a.customer_id', 'c.customer_name', 'c.customer_email')
            ->orderBy('a.date', 'desc')
            ->get();

        if ($appointments->isEmpty()) {
            $this->info("No appointments found for yesterday.");
            
                 file_put_contents(
                    storage_path() . '/logs/command1.log',
                    "after In minute loop : " . PHP_EOL,
                    FILE_APPEND | LOCK_EX
                );
                
        } else {
            foreach ($appointments as $appointment) {
                // Safely get customer email
                $customerEmail = $appointment->customer_email ?? 'N/A';
                $customerName  = $appointment->customer_name ?? 'Customer';
                $serviceNames  = $appointment->service_names ?? 'Service';

                $this->info("Appointment found: ID {$appointment->appointment_id}, Customer Email: {$customerEmail}, Service: {$serviceNames}");

                // Send email
                Mail::to($customerEmail)
                    ->send(new ServiceFollowupMail($customerName, $serviceNames));

                $this->info("Test email sent to {$customerEmail} successfully!");
                
                 file_put_contents(
                    storage_path() . '/logs/command.log',
                    "after In minute loop : " . PHP_EOL,
                    FILE_APPEND | LOCK_EX
                );
            }
        }

        return Command::SUCCESS;
    }
}

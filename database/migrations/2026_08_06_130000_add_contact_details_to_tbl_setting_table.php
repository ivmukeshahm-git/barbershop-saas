<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Previously applied with contact_address / contact_phone / contact_email / support_email.
 * Kept as a no-op so migrate history stays valid.
 */
return new class extends Migration
{
    public function up()
    {
        // Columns already exist from earlier apply of this migration name.
    }

    public function down()
    {
        //
    }
};

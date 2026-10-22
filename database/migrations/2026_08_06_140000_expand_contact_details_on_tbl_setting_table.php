<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_setting', function (Blueprint $table) {
            $table->text('footer_about')->nullable()->after('support_email');
            $table->string('whatsapp', 30)->nullable()->after('footer_about');
            $table->string('website_url', 255)->nullable()->after('whatsapp');
            $table->string('facebook_url', 255)->nullable()->after('website_url');
            $table->string('instagram_url', 255)->nullable()->after('facebook_url');
            $table->string('tiktok_url', 255)->nullable()->after('instagram_url');
            $table->string('youtube_url', 255)->nullable()->after('tiktok_url');
            $table->string('twitter_url', 255)->nullable()->after('youtube_url');
            $table->string('linkedin_url', 255)->nullable()->after('twitter_url');
        });

        DB::table('tbl_setting')->where('id', 1)->update([
            'footer_about' => 'Blue Crown Barbers delivers premium grooming in a refined, modern studio. Classic cuts, precise fades, and a luxury experience crafted for every client.',
            'whatsapp' => '15875552489',
            'website_url' => 'https://www.bluecrownbarbers.com',
            'facebook_url' => 'https://www.facebook.com/bluecrownbarbers',
            'instagram_url' => 'https://www.instagram.com/bluecrownbarbers',
            'tiktok_url' => 'https://www.tiktok.com/@bluecrownbarbers',
            'youtube_url' => 'https://www.youtube.com/@bluecrownbarbers',
            'twitter_url' => 'https://twitter.com/bluecrownbarbers',
            'linkedin_url' => 'https://www.linkedin.com/company/bluecrownbarbers',
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_setting', function (Blueprint $table) {
            $table->dropColumn([
                'footer_about',
                'whatsapp',
                'website_url',
                'facebook_url',
                'instagram_url',
                'tiktok_url',
                'youtube_url',
                'twitter_url',
                'linkedin_url',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RIQX: the Ed25519 signature the app checks before it runs a Windows installer
 * from inside itself (docs/12-app-update.md). Null for anything uploaded by
 * hand: the app then only offers the download page.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_downloads', function (Blueprint $table) {
            $table->string('update_signature', 128)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('client_downloads', function (Blueprint $table) {
            $table->dropColumn('update_signature');
        });
    }
};

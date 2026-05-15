<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feature_flags', function (Blueprint $table): void {
            // Audience targeting: list of {attribute, values} clauses (AND-ed).
            // Empty array means all users are eligible.
            $table->json('attribute_rules')->default('[]')->after('enabled');

            // Fraction of eligible users that see the feature (0–100).
            // Null means 100 % — no percentage gate applied.
            $table->unsignedSmallInteger('rollout_percentage')->nullable()->after('attribute_rules');

            // Optional activation window.  Null means no boundary on that side.
            $table->timestampTz('starts_at')->nullable()->after('rollout_percentage');
            $table->timestampTz('ends_at')->nullable()->after('starts_at');
        });
    }

    public function down(): void
    {
        Schema::table('feature_flags', function (Blueprint $table): void {
            $table->dropColumn(['attribute_rules', 'rollout_percentage', 'starts_at', 'ends_at']);
        });
    }
};

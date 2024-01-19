<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('articles_tags', 'article_tag');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('article_tag', 'articles_tags');
    }
};

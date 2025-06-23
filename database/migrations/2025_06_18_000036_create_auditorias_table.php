
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAuditoriasTable extends Migration
{
    public function up()
    {
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id('idAuditoria');
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id');
            $table->string('event');
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_type')->nullable();
            $table->string('tags')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('url')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->index(['auditable_type', 'auditable_id'], 'auditorias_auditable_type_auditable_id_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('auditorias');
    }
}

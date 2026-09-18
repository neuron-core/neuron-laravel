<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Identifiers are hex-encoded by the persistence backend: 255 bytes -> 510 chars.
        $isMysql = in_array(Schema::getConnection()->getDriverName(), ['mysql', 'mariadb'], true);

        Schema::create('workflow_store', function (Blueprint $table) use ($isMysql): void {
            $partition = $table->string('partition', 510);
            $key = $table->string('key', 510);
            $value = $table->longText('value');

            // MySQL needs the single-byte ascii charset so the composite primary
            // key fits inside the index size limit; other drivers have no such limit.
            if ($isMysql) {
                $partition->charset('ascii')->collation('ascii_bin');
                $key->charset('ascii')->collation('ascii_bin');
                $value->charset('ascii');
            }

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->primary(['partition', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_store');
    }
};

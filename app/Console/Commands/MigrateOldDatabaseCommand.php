<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateOldDatabaseCommand extends Command
{
    protected $signature = 'db:migrate-old
        {--tables=* : Specific tables to migrate (comma-separated)}
        {--dry-run : Preview migration without inserting}
        {--chunk=500 : Number of records per batch (for performance)}';

    protected $description = 'Migrate data from old Postgres DB to new one with table/column mapping, ID preservation, verification, and chunked inserts';

    public function handle()
    {
        $old = DB::connection('pgsql_old');
        $new = DB::connection();

        $this->insertBaseUsers();

        /**
         * Define table + column mappings:
         * 'old_table' => [
         * 'table' => 'new_table',
         * 'columns' => ['old_col' => 'new_col', ...]
         * ]
         */
        $tableMappings = [
            'clients' => [
                'table' => 'clients',
                'columns' => [
                    'id' => 'id',
                    'name' => 'name',
                    'ice' => 'ice',
                    'phone' => 'phone',
                    'fax' => 'fax',
                    'email' => 'email',
                    'adress' => 'address',
                    'secteur' => 'sector',
                    'user_id' => 'user_id',
                    'created_at' => 'created_at',
                    'updated_at' => 'updated_at',
                ],
            ],
            'factures' => [
                'table' => 'invoices',
                'columns' => [
                    'id' => 'id',
                    'totalHT' => 'total',
                    'client_id' => 'client_id',
                    'user_id' => 'user_id',
                    'created_at' => 'created_at',
                    'updated_at' => 'updated_at',
                ],
            ],
            'fournisseurs' => [
                'table' => 'suppliers',
                'columns' => [
                    'id' => 'id',
                    'name' => 'name',
                    'phone' => 'phone',
                    'adress' => 'address',
                    'email' => 'email',
                    'contact' => 'contact',
                    'specialite' => 'category',
                    'created_at' => 'created_at',
                    'updated_at' => 'updated_at',
                ],
            ],
            'categories' => [
                'table' => 'product_categories',
                'columns' => [
                    'id' => 'id',
                    'name' => 'name',
                    'created_at' => 'created_at',
                    'updated_at' => 'updated_at',
                ],
            ],
            'produits' => [
                'table' => 'products',
                'columns' => [
                    'id' => 'id',
                    'name' => 'name',
                    'delaisLivraison' => 'delivery_time',
                    'prixFournisseur' => 'supplier_price',
                    'prixVente' => 'price',
                    'quantiteMin' => 'min_qty',
                    'quantiteMax' => 'max_qty',
                    'note' => 'note',
                    'img' => 'img',
                    'category_id' => 'product_category_id',
                    'fournisseur_id' => 'supplier_id',
                    'created_at' => 'created_at',
                    'updated_at' => 'updated_at',
                ],
            ],
            'facturesproduits' => [
                'table' => 'invoice_products',
                'columns' => [
                    'id' => 'id',
                    'quantite' => 'qty',
                    'prixHT' => 'price',
                    'produit_id' => 'product_id',
                    'facture_id' => 'invoice_id',
                    'created_at' => 'created_at',
                    'updated_at' => 'updated_at',
                ],
            ],
            // Add more mappings here...
        ];

        $selectedTables = $this->option('tables') ?: array_keys($tableMappings);
        $dryRun = $this->option('dry-run');
        $chunkSize = (int) $this->option('chunk');

        $results = [];

        foreach ($selectedTables as $oldTable) {
            if (!isset($tableMappings[$oldTable])) {
                $this->warn("⚠ Skipping {$oldTable}: not defined in tableMappings.");
                continue;
            }

            $config = $tableMappings[$oldTable];
            $newTable = $config['table'];
            $mapping = $config['columns'];

            $columnsOld = array_keys($mapping);
            $columnsNew = array_values($mapping);

            $countOld = $old->table($oldTable)->count();
            if ($countOld === 0) {
                $this->warn("⚠ No records found in {$oldTable}");
                continue;
            }

            $this->info("🚀 Migrating {$countOld} records from '{$oldTable}' → '{$newTable}'");

            if ($dryRun) {
                $this->line("Dry-run mode enabled — no data inserted.");
                continue;
            }

            $bar = $this->output->createProgressBar($countOld);
            $bar->start();

            $old->table($oldTable)
                ->orderBy('id')
                ->chunk($chunkSize, function ($rows) use ($new, $newTable, $mapping, $bar) {
                    $insertBatch = [];

                    foreach ($rows as $row) {
                        $record = [];
                        foreach ($mapping as $oldCol => $newCol) {
                            $record[$newCol] = $row->$oldCol;
                        }
                        $insertBatch[] = $record;
                    }

                    if (!empty($insertBatch)) {
                        $new->table($newTable)->insert($insertBatch);
                    }

                    $bar->advance(count($rows));
                });

            $bar->finish();
            $this->newLine();

            // Reset auto-increment sequence
            $this->resetSequence($new, $newTable);

            // Run verification checks
            $verification = $this->verifyMigration($old, $new, $oldTable, $newTable, $mapping);
            $results[$newTable] = $verification;

            if ($verification['status'] === 'OK') {
                $this->info("✅ Verified: {$oldTable} → {$newTable}");
            } else {
                $this->error("❌ Verification failed for {$oldTable} → {$newTable}");
            }
        }

        $this->showSummary($results);
    }

    protected function insertBaseUsers()
    {
        $count = User::all()->count();
        if ($count === 0) {
            User::create([
                'name' => 'Hamza Merrahi',
                'email' => 'hamza@hsprint.ma',
                'password' => 'hamza123',
                'role' => 'admin'
            ]);
            User::create([
                'name' => 'Abderrahmane Badry',
                'email' => 'badry.abderrahmane@gmail.com',
                'password' => 'badry123',
                'role' => 'admin'
            ]);
        }
    }

    /**
     * Reset PostgreSQL sequence after manual ID inserts.
     */
    protected function resetSequence($conn, $table)
    {
        try {
            $conn->statement("
                SELECT setval(
                pg_get_serial_sequence('\"{$table}\"', 'id'),
                COALESCE((SELECT MAX(id) FROM \"{$table}\"), 1)
                )
            ");
            $this->line("↳ Sequence reset for {$table}");
        } catch (\Exception $e) {
            $this->warn("⚠ Could not reset sequence for {$table}: {$e->getMessage()}");
        }
    }

    /**
     * Verify migration by comparing record counts, ID ranges, and sample data.
     */
    protected function verifyMigration($old, $new, $oldTable, $newTable, $mapping)
    {
        $result = [
            'status' => 'OK',
            'count_old' => 0,
            'count_new' => 0,
            'first_id_match' => true,
            'last_id_match' => true,
        ];

        try {
            $countOld = $old->table($oldTable)->count();
            $countNew = $new->table($newTable)->count();

            $result['count_old'] = $countOld;
            $result['count_new'] = $countNew;

            if ($countOld !== $countNew) {
                $result['status'] = 'COUNT_MISMATCH';
                return $result;
            }

            // Compare min/max ID values
            $minOld = $old->table($oldTable)->min('id');
            $maxOld = $old->table($oldTable)->max('id');
            $minNew = $new->table($newTable)->min($mapping['id'] ?? 'id');
            $maxNew = $new->table($newTable)->max($mapping['id'] ?? 'id');

            $result['first_id_match'] = ($minOld === $minNew);
            $result['last_id_match'] = ($maxOld === $maxNew);

            if (!$result['first_id_match'] || !$result['last_id_match']) {
                $result['status'] = 'ID_RANGE_MISMATCH';
                return $result;
            }

            // Optional: Spot check a few random IDs for data consistency
            $sampleIds = $old->table($oldTable)
                ->inRandomOrder()
                ->limit(3)
                ->pluck('id');

            foreach ($sampleIds as $id) {
                $oldRow = (array) $old->table($oldTable)->where('id', $id)->first();
                $newRow = (array) $new->table($newTable)->where($mapping['id'] ?? 'id', $id)->first();

                if (empty($newRow)) {
                    $result['status'] = 'MISSING_RECORD';
                    return $result;
                }

                // Compare mapped columns
                foreach ($mapping as $oldCol => $newCol) {
                    if (isset($oldRow[$oldCol]) && isset($newRow[$newCol])) {
                        if ((string)$oldRow[$oldCol] !== (string)$newRow[$newCol]) {
                            $result['status'] = 'DATA_MISMATCH';
                            return $result;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $result['status'] = 'ERROR';
        }

        return $result;
    }

    /**
     * Show a clean summary of all verifications.
     */
    protected function showSummary(array $results)
    {
        $this->newLine(2);
        $this->info('📊 Migration Verification Summary');
        $this->line(str_repeat('─', 50));

        foreach ($results as $table => $res) {
            $status = match ($res['status']) {
                'OK' => '✅ OK',
                'COUNT_MISMATCH' => '❌ Count mismatch',
                'ID_RANGE_MISMATCH' => '⚠️ ID range mismatch',
                'DATA_MISMATCH' => '❌ Data mismatch',
                'MISSING_RECORD' => '❌ Missing record(s)',
                'ERROR' => '⚠️ Error',
                default => '⚠️ Unknown',
            };

            $this->line(sprintf(
                "%-20s | Old: %-6d | New: %-6d | %s",
                $table,
                $res['count_old'] ?? 0,
                $res['count_new'] ?? 0,
                $status
            ));
        }

        $this->line(str_repeat('─', 50));
        $this->info('✅ Migration completed with verification checks.');
    }
}

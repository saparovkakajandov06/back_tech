<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OptimizeMigrations extends Command
{
    protected $signature = 'smm:opt';

    protected $description = "optimize db 10 sep 2021";

    public function handle()
    {
        // removed 14 tables:
        // 2019_04_29_065139_create_services_table.php
        // 2019_05_05_071551_create_orders_table.php
        // 2019_09_10_084567_create_tags_table.php
        // 2019_09_10_084038_create_posts_table.php
        // 2019_09_10_085608_create_posts_tags_table.php
        // 2019_09_29_005654_create_notifications_table.php
        // 2019_10_01_135047_create_notification_status_table.php
        // 2019_09_29_174635_create_prices_table.php
        // 2019_09_30_174504_create_price_feature_table.php
        // 2019_09_29_174304_create_features_table.php
        // 2020_06_15_161605_create_transaction_groups_table.php
        // 2020_06_15_161613_create_transaction_types_table.php
        // 2019_09_29_174634_create_categories_table.php
        // 2020_02_23_131003_create_configs_table.php

        // removed modifications:
        // 2020_05_21_130529_add_commission_to_transactions_table.php
        // 2020_07_18_193841_add_uuid_to_composite_orders.php
        // 2020_08_16_045201_add_telegram_id_to_users_table.php
        // 2020_08_30_075945_add_platform_and_name_to_user_services_table.php
        // 2020_09_17_162428_add_created_at_index_to_composite_orders.php
        // 2020_09_30_155947_migrate_composite_orders_params_to_jsonb.php
        // 2020_10_26_053759_convert_pipelines.php
        // 2020_10_28_205324_add_cur_to_transactions.php
        // 2020_11_04_144936_add_cur_to_premium_statuses.php
        // 2020_11_05_064951_remove_price_list_from_user_services.php
        // 2020_11_28_192138_add_clients_to_user_services.php
        // 2020_11_30_045036_add_labels_to_user_services.php
        // 2020_12_05_035719_add_lang_and_currency_to_users.php
        // 2021_05_02_174822_update_chunks_convert_details_to_jsonb.php
        // 2021_05_21_121510_remove_fields_from_user_services.php
        // 2021_08_19_083741_add_params_to_users_table.php

        $tablesToDelete = [
            'posts_tags', 'posts', 'tags',
            'services', 'orders',
            'notification_status', 'notifications',
            'price_feature', 'prices',
            'features',
            'transaction_types', 'transaction_groups',
            'categories',
            'configs',
        ];

        foreach($tablesToDelete as $table) {
            if (Schema::hasTable($table)) {
                echo "table $table exists\n";
                Schema::drop($table);
            } else {
                echo "error\n";
            }
        }

        $migrationsToDelete = [
            '2019_04_29_065139_create_services_table',
            '2019_05_05_071551_create_orders_table',
            '2019_09_10_084567_create_tags_table',
            '2019_09_10_084038_create_posts_table',
            '2019_09_10_085608_create_posts_tags_table',
            '2019_09_29_005654_create_notifications_table',
            '2019_10_01_135047_create_notification_status_table',
            '2019_09_29_174635_create_prices_table',
            '2019_09_30_174504_create_price_feature_table',
            '2019_09_29_174304_create_features_table',
            '2020_06_15_161605_create_transaction_groups_table',
            '2020_06_15_161613_create_transaction_types_table',
            '2019_09_29_174634_create_categories_table',
            '2020_02_23_131003_create_configs_table',

            '2020_05_21_130529_add_commission_to_transactions_table',
            '2020_07_18_193841_add_uuid_to_composite_orders',
            '2020_08_16_045201_add_telegram_id_to_users_table',
            '2020_08_30_075945_add_platform_and_name_to_user_services_table',
            '2020_09_17_162428_add_created_at_index_to_composite_orders',
            '2020_09_30_155947_migrate_composite_orders_params_to_jsonb',
            '2020_10_26_053759_convert_pipelines',
            '2020_10_28_205324_add_cur_to_transactions',
            '2020_11_04_144936_add_cur_to_premium_statuses',
            '2020_11_05_064951_remove_price_list_from_user_services',
            '2020_11_28_192138_add_clients_to_user_services',
            '2020_11_30_045036_add_labels_to_user_services',
            '2020_12_05_035719_add_lang_and_currency_to_users',
            '2021_05_02_174822_update_chunks_convert_details_to_jsonb',
            '2021_05_21_121510_remove_fields_from_user_services',
            '2021_08_19_083741_add_params_to_users_table',
        ];

        foreach($migrationsToDelete as $m) {
            $record = DB::table('migrations')
                ->where('migration', $m)
                ->first();

            $id = $record?->id;
            if (! $id) {
                echo "=========== migration $m not found!\n";
            } else {
                echo "deleting migration $m\n";
                DB::table('migrations')->delete($id);
            }
        }
    }
}

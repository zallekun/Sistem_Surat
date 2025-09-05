<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNotificationSettingsToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'notification_settings' => [
                'type' => 'JSON',
                'null' => true,
                'after' => 'workload',
            ],
        ];
        
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'notification_settings');
    }
}
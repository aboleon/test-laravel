<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
      CREATE OR REPLACE VIEW front_my_orders_view AS
      
SELECT
    'order' as type,
    o.event_id as event_id,
    o.id as order_id,
    o.uuid,
    o.client_id as client_id,
    o.client_type as client_type,
    o.created_at AS date,
    o.total_net,
    o.total_vat,
    o.total_net + o.total_vat AS total_ttc,
    o.total_pec,
    oi.id as order_invoice_id
   
FROM orders o
LEFT JOIN order_invoices oi on oi.order_id = o.id   
   
UNION ALL

SELECT 
    'refund' as type,
    rv.event_id as event_id,
    rv.order_id as order_id,
    rv.uuid,
    rv.client_id as client_id,
    rv.client_type as client_type,    
    rv.created_at_raw AS date, 
    CAST((rv.total_raw / (1 + rv.vat_rate_raw / 10000)) AS SIGNED) as total_net, 
    CAST((rv.total_raw - rv.total_raw / (1 + rv.vat_rate_raw / 10000)) AS SIGNED) as total_vat,
    rv.total_raw as total_ttc,   
    0 as total_pec, 
    NULL as order_invoice_id
    
FROM order_refunds_view rv
    
   
   
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS front_my_orders_view");
    }
};

<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// envia as vendas vendas do dia para a fila à meia noite
Schedule::command('sales:publish')->daily();

// descomente a linha a seguir se quiser receber o email de vendas a cada 10 segundos
//Schedule::command('sales:publish')->everyTenSeconds();

<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

Artisan::command('sistema:backup-windows', function () {
    $this->info('--- Iniciando Proceso de Backup Limpio ---');
    
    // 1. LIMPIEZA PREVIA: Borramos cualquier ZIP viejo antes de empezar
    // Buscamos en storage/app y subcarpetas para no dejar rastros
    $archivosViejos = File::allFiles(storage_path('app'));
    foreach ($archivosViejos as $archivo) {
        if ($archivo->getExtension() === 'zip') {
            File::delete($archivo->getPathname());
            $this->warn('Archivo antiguo eliminado: ' . $archivo->getFilename());
        }
    }

    // 2. EJECUTAR EL BACKUP (Solo base de datos)
    // Al haber borrado antes, el archivo que se cree ahora será el único.
    Artisan::call('backup:run', ['--only-db' => true]);
    $this->info('Backup nuevo generado correctamente.');

    // 3. LOCALIZAR EL NUEVO ARCHIVO PARA CONFIRMAR
    $archivosNuevos = File::allFiles(storage_path('app'));
    $rutaFinal = "";
    
    foreach ($archivosNuevos as $archivo) {
        if ($archivo->getExtension() === 'zip') {
            $rutaFinal = $archivo->getPathname();
            break;
        }
    }

    if ($rutaFinal) {
        $this->info('LISTO: El archivo actual es: ' . $rutaFinal);
        $this->comment('Ya puedes copiarlo manualmente a tu disco externo.');
    } else {
        $this->error('No se pudo generar el archivo.');
    }
});

// Programación automática: Todos los días a la medianoche
Schedule::command('sistema:backup-windows')->dailyAt('00:00');

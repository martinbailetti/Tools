<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanFontCache extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'fonts:clean-cache {--force : Forzar limpieza sin confirmación}';

    /**
     * The console command description.
     */
    protected $description = 'Limpia el cache de fuentes duplicadas generado por DomPDF';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando limpieza de cache de fuentes...');

        // Rutas donde buscar archivos de fuentes
        $fontPaths = [
            resource_path('fonts'),
            storage_path('fonts'),
            base_path('storage/fonts'),
            storage_path('app/dompdf'),
        ];

        $totalCleaned = 0;
        $totalScanned = 0;

        foreach ($fontPaths as $fontPath) {
            if (is_dir($fontPath)) {
                $this->info("Escaneando: {$fontPath}");
                $result = $this->cleanFontDirectory($fontPath);
                $totalScanned += $result['scanned'];
                $totalCleaned += $result['cleaned'];
            } else {
                $this->warn("Directorio no encontrado: {$fontPath}");
            }
        }

        $this->info("Limpieza completada:");
        $this->info("- Archivos escaneados: {$totalScanned}");
        $this->info("- Archivos duplicados eliminados: {$totalCleaned}");

        if ($totalCleaned > 0) {
            $this->info('✅ Cache de fuentes limpiado exitosamente');
        } else {
            $this->info('ℹ️  No se encontraron duplicados para limpiar');
        }
    }

    /**
     * Limpia un directorio específico de fuentes
     */
    private function cleanFontDirectory($directory)
    {
        $scanned = 0;
        $cleaned = 0;
        $duplicates = [];

        if (!is_dir($directory)) {
            return ['scanned' => 0, 'cleaned' => 0];
        }

        $files = glob($directory . '/*');

        foreach ($files as $file) {
            if (is_file($file)) {
                $scanned++;
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $filename = pathinfo($file, PATHINFO_FILENAME);

                // Buscar archivos con hash duplicados
                if (in_array($extension, ['ufm', 'afm', 'ttf'])) {
                    // Si el archivo tiene un hash MD5/SHA al final
                    if (preg_match('/_[a-f0-9]{32}$/', $filename)) {
                        $cleanName = preg_replace('/_[a-f0-9]{32}$/', '', $filename);
                        $cleanFile = $directory . '/' . $cleanName . '.' . $extension;

                        // Si existe la versión limpia, marcar para eliminación
                        if (file_exists($cleanFile)) {
                            $duplicates[] = $file;
                        }
                    }
                }
            }
        }

        // Mostrar duplicados encontrados
        if (!empty($duplicates)) {
            $this->warn("Encontrados " . count($duplicates) . " archivos duplicados en: {$directory}");

            foreach ($duplicates as $duplicate) {
                $this->line("  - " . basename($duplicate));
            }

            if ($this->option('force') || $this->confirm('¿Eliminar estos archivos duplicados?')) {
                foreach ($duplicates as $duplicate) {
                    if (unlink($duplicate)) {
                        $cleaned++;
                        $this->line("✅ Eliminado: " . basename($duplicate));
                    } else {
                        $this->error("❌ Error eliminando: " . basename($duplicate));
                    }
                }
            }
        }

        return ['scanned' => $scanned, 'cleaned' => $cleaned];
    }
}

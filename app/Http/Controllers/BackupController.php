<?php

namespace App\Http\Controllers;
use Spatie\Backup\BackupDestination\Backup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class BackupController extends Controller
{
    public function backup()
    {
        try {
            Artisan::call('backup:run');
            return response()->json(['message' => 'Backup completed successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

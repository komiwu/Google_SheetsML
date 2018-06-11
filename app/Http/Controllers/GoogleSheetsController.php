<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classes\GoogleSheets;

class GoogleSheetsController extends Controller
{
    public function getGoogleSheets() {
        // TODO . Use route parameters to select the paths we chose...
        $basePath = 'Google_Sheets_batchUpdates\\';
        $exercise = 'firstExample\\';
        $paths = ['values_batch' => resource_path($basePath.$exercise.'spreadsheets.values.batchUpdate.json'),
                  'chart_batch' => resource_path($basePath.$exercise.'spreadsheets.chart.batchUpdate.json')];

        $google_sheet = new GoogleSheets;
        $google_sheet->createSpreadsheet();
        $google_sheet->populateGoogleSpreadsheet($paths);
        $google_sheet->setGoogleSpreadsheetPermissions();

        return view('google_sheets')->with('results', $google_sheet->spreadsheet);
    }
}

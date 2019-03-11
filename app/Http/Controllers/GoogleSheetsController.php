<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classes\GoogleSheets;

class GoogleSheetsController extends Controller
{
   /** getGoogleSheets($url_string)
    * @param $url_string - The actual {id} from Router. Used to specify which view to return
    * Uses Google API to create a fresh, empty spreadsheet
    * Sets permissions to "everybody"
    *
    * @return view (with the spreadsheet object as parameter)
    */
    public function getGoogleSheets($url_string) {
        $google_sheet = new GoogleSheets; //Connects the object to Google API
        $google_sheet->createSpreadsheet(); //creates a brand new spreadsheet
        $google_sheet->setGoogleSpreadsheetPermissions(); //sets default permissions : everyone, read/write

        return view($url_string)->with('results', $google_sheet->spreadsheet);
    }

   /** refreshSheetValues(Request $request)
    * @param $request - POST/GET variable contents. Used to obtain the spreadsheetId passed through ajax.
    * Refresh all volatile functions on a spreadsheet
    *
    * @return null (effects the spreadsheet directly)
    */
    public function refreshSheetValues(Request $request) {
      //TODO: Find a way to do this w/o having to refresh the values twice.
      //We need to be able to trigger the refresh of volatile functions just once; and without effecting the rest of the spreadSheet

      $spreadSheetId = $request->input('spreadsheetId');
      $google_sheet = new GoogleSheets; //establish a connection to Google API
      $google_sheet->getSpreadsheet($spreadSheetId); //fill objects member variables for the spreadsheet with ID : $spreadSheetId);

      $range = "A1:A1"; //lets select the value we want to extract. We will use cell A1 for this.
      $savedValueRange = $google_sheet->getValues($spreadSheetId, $range); //save the current valueRange that is in the cell
      $saved_value = $savedValueRange->values; //$savedValueRange->getValues() can also be used
      //$saved_value comes in the format [[value]]

      do{ //lets generate a random number that isnt equal to the saved_value
        $rand_value = mt_rand(1,100);
      } while($rand_value==$saved_value[0][0]);

      $google_sheet->setValues($spreadSheetId, $range, Array(Array($rand_value))); //Lets replace the value in A1 with our random value
      $google_sheet->setValues($spreadSheetId, $range, $saved_value); //Lets place back the original contents of A1
      //This will cause the cells with '=RAND(...)' to be refreshed twice.
    }

   /** populateSpreadsheet(Request $request)
    * @param $request - POST/GET variable contents. Used to obtain the spreadsheetId passed through ajax.
    * Do a batch update on the spreadsheet; filling in values, setting cell formats, drawing charts, etc.,
    * This uses json files
    *
    * @return null (effects the spreadsheet directly)
    */
    public function populateSpreadsheet(Request $request) {
      $spreadsheetId = $request->input('spreadsheetId');

      $google_sheet = new GoogleSheets; //establish a connection to Google API
      $google_sheet->getSpreadsheet($spreadsheetId); //fill objects member variables for the spreadsheet with ID : $spreadSheetId);

      //set paths for the json files
      $basePath = 'Google_Sheets_batchUpdates\\';
      $exercise = 'firstExample\\';
      $paths = ['values_batch' => resource_path($basePath.$exercise.'spreadsheets.values.batchUpdate.json'),
                'cellBackgroundColor_batch' => resource_path($basePath.$exercise.'spreadsheets.cell.backgroundColor.batchUpdate.json'),
                'cellFormat_batch' => resource_path($basePath.$exercise.'spreadsheets.cell.format.batchUpdate.json'),
                'chart_batch' => resource_path($basePath.$exercise.'spreadsheets.chart.batchUpdate.json'),
                'protectedRange_batch' => resource_path($basePath.$exercise.'spreadsheets.cell.protectedRange.batchUpdate.json')];
      //call the batch update function
      $google_sheet->populateGoogleSpreadsheet($paths);
    }

    //Just a testing function
    public function test(Request $request) {
      $spreadsheetId = $request->input('spreadsheetId');

      $google_sheet = new GoogleSheets;
      $google_sheet->getSpreadsheet($spreadsheetId);

      $google_sheet->test();
    }
}

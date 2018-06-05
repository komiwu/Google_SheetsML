<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

//require_once(base_path('vendor/google/apiclient/src/Google/autoload.php'));
require(base_path('vendor/autoload.php'));

class GoogleSheetsController extends Controller
{
    public function getGoogleSheets() {
        //
        $credentials = resource_path('php_libraries\Google_Sheets_API\credentials\credentials.json');
        $client_secret = resource_path('php_libraries\Google_Sheets_API\credentials\client_secret.json');

        $client = new \Google_Client();
        $client->setApplicationName('crypto-symbol-205814');
        $client->setScopes(['https://www.googleapis.com/auth/spreadsheets',
                            'https://www.googleapis.com/auth/drive',
                            'https://spreadsheets.google.com/feeds']);
        $client->setAccessType('online');
        //$client->setAuthConfig($client_secret);
        $client->setAuthConfig($credentials);

        $service = new \Google_Service_Sheets($client);

        // TODO: Assign values to desired properties of `requestBody`:
        $requestBody = new \Google_Service_Sheets_Spreadsheet();

        $response = $service->spreadsheets->create($requestBody);
        $spreadsheetId = $response->spreadsheetId;

        //populate the spreadsheet
        $this->populateGoogleSpreadsheet($spreadsheetId, $service, $response);

        //Set view/edit permissions for our spreadsheet
        $this->setGoogleSpreadsheetPermissions($spreadsheetId, $client);

        $results = $response;
        return view('google_sheets')->with('results', $results);
    }

    /*Lets set permissions for our spreadhsheet by using the Drive Service
     *********************************************************************
     *********************************************************************
     *********************************************************************/
    private function setGoogleSpreadsheetPermissions($spreadsheetId, $client) {
      $driveService = new \Google_Service_Drive($client);
      $driveService->getClient()->setUseBatch(true);

      try {
          $batch = $driveService->createBatch();

          $userPermission = new \Google_Service_Drive_Permission(array(
              'type' => 'anyone',
              'role' => 'writer',
              'notify' => false
          ));
          $request = $driveService->permissions->create(
              $spreadsheetId, $userPermission, array('fields' => 'id'));
          $batch->add($request, 'user');
          $domainPermission = new \Google_Service_Drive_Permission(array(
              'type' => 'anyone',
              'role' => 'writer',
              'notify' => false
          ));
          $request = $driveService->permissions->create(
              $spreadsheetId, $domainPermission, array('fields' => 'id'));
          $batch->add($request, 'domain');
          $results = $batch->execute();

          foreach ($results as $result) {
              if ($result instanceof \Google_Service_Exception) {
                  // Handle error
                  printf($result);
              } else {
                  printf("Permission ID: %s\n", $result->id);
              }
          }
      } finally {
          $driveService->getClient()->setUseBatch(false);
      }
    } //end setGoogleSpreadsheetPermissions($spreadsheetId, $client)

    /*Lets populate our spreadsheet using the Google Sheets service
     *********************************************************************
     *********************************************************************
     *********************************************************************/
    private function populateGoogleSpreadsheet($spreadsheetId, $service, $spreadsheet) {
        //we can store store data we want to use in DB and then use a query to
        //retrieve it based on which page called it

        //For now, lets just hardcode an example
        $values = [
      		['Model Number','Sales - Jan','Sales - Feb', 'Sales - Mar', 'Total Sales'],
          ['D-01X', '=FLOOR(100*RAND())', 74, '60', '=SUM(B2:D2)'],
          ['FR-0B1', '=FLOOR(100*RAND())', 76, '88', '=SUM(B3:D3)'],
          ['P-034', '=FLOOR(100*RAND())', 49, '32', '=SUM(B4:D4)'],
          ['P-105', '=FLOOR(100*RAND())', 44, '67', '=SUM(B5:D5)'],
          ['W-11', '=FLOOR(100*RAND())', 68, '87', '=SUM(B6:D6)'],
          ['W-22', '=FLOOR(100*RAND())', 52, '62', '=SUM(B7:D7)'],
        ];

        $range = "A1:E8";

        $body = new \Google_Service_Sheets_ValueRange([
          'values' => $values
        ]);

        //valueInputOption parameter documentation : https://developers.google.com/sheets/api/reference/rest/v4/ValueInputOption
        $valueInputOption = 'USER_ENTERED';

        $params = [
          'valueInputOption' => $valueInputOption
        ];

        //spreadsheets_values->update documentation : https://developers.google.com/sheets/api/reference/rest/v4/spreadsheets.values#ValueRange
        $result = $service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);

        //get the ID of the individual sheet inside of the Google Spreadsheet
        $sourseId = $spreadsheet->getSheets()[0]->getProperties()->getSheetId();

        //Create the request object.
        //TODO: Enter this request into a json object, and import it instead of hardcoding it here
        //request samples can be found here: https://developers.google.com/sheets/api/samples/charts
        //Google_Service_Sheets_Request usage (limited) can be found here: https://developers.google.com/sheets/api/guides/batchupdate#example
        $requests = new \Google_Service_Sheets_Request([
          'addChart' => [
            'chart' => [
              'spec' => [
                'title' => "Model Q1 Total Sales",
                'pieChart' => [
                  'legendPosition' => "RIGHT_LEGEND",
                  'threeDimensional' => true,
                  'domain' => [
                    'sourceRange' => [
                      'sources' => [
                        'sheetId' => $sourseId,
                        'startRowIndex' => 0,
                        'endRowIndex' => 7,
                        'startColumnIndex' => 0,
                        'endColumnIndex' => 1
                      ]
                    ]
                  ],
                  'series' => [
                    'sourceRange' => [
                      'sources' => [
                        'sheetId' => $sourseId,
                        'startRowIndex' => 0,
                        'endRowIndex' => 7,
                        'startColumnIndex' => 4,
                        'endColumnIndex' => 5
                      ]
                    ]
                  ],
                ]
              ],
              'position' => [
                'overlayPosition' => [
                  'anchorCell' => [
                    'sheetId' => $sourseId,
                    'rowIndex' => 5,
                    'columnIndex' => 2
                  ],
                  'offsetXPixels' => 50,
                  'offsetYPixels' => 50
                ]
              ]
            ]
          ]
        ]);

        $batchUpdataRequest = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
          'requests' => $requests
        ]);

        $result2 = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdataRequest);
    }
}

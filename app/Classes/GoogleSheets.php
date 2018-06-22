<?php

namespace App\Classes;
//require_once(base_path('vendor/google/apiclient/src/Google/autoload.php'));
require(base_path('vendor/autoload.php'));

class GoogleSheets {

  private $credentials;
  private $client_secret;

  //https://github.com/google/google-api-php-client
  public $client;
  //https://developers.google.com/resources/api-libraries/documentation/sheets/v4/php/latest/class-Google_Service_Sheets.html
  public $service;
  //https://developers.google.com/resources/api-libraries/documentation/sheets/v4/php/latest/class-Google_Service_Sheets_Spreadsheet.html
  public $requestBody;

  //spreadsheet object
  public $spreadsheet;
  //spreadsheet ID
  public $spreadsheetId;
  //main spreadsheet within the spreadsheet object
  public $innerSpreadsheet;


  /**
   * Create a new GoogleSheets instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->credentials = resource_path('\php_libraries\Google_Sheets_API\credentials\credentials.json');
    $this->client_secret = resource_path('\php_libraries\Google_Sheets_API\credentials\client_secret.json');

    $this->client = new \Google_Client();
    $this->client->setApplicationName('crypto-symbol-205814');
    $this->client->setScopes(['https://www.googleapis.com/auth/spreadsheets',
                        'https://www.googleapis.com/auth/drive',
                        'https://spreadsheets.google.com/feeds']);
    $this->client->setAccessType('online');
    //$client->setAuthConfig($client_secret);
    $this->client->setAuthConfig($this->credentials);

    $this->service = new \Google_Service_Sheets($this->client);
  }

  /**
   * Tell Google API to create a new spreadsheet.
   *
   * @return void
   */
  public function createSpreadsheet() {
    // TODO: Assign values to desired properties of `requestBody`:
    $this->requestBody = new \Google_Service_Sheets_Spreadsheet();

    $this->spreadsheet = $this->service->spreadsheets->create($this->requestBody);
    $this->spreadsheetId = $this->spreadsheet->spreadsheetId;
    //get the ID of the individual sheet inside of the Google Spreadsheet
    //$this->spreadSheet->getSheets()[0]->getProperties()->getSheetId();
    $this->innerSpreadsheet = $this->spreadsheet->getSheets()[0];
  }

  /**
   * Populate the spreadsheet with defined values and options.
   *
   * @return void
   */
  public function populateGoogleSpreadsheet($requestPaths) {
    // The ID of the spreadsheet to update.
    $spreadsheetId = $this->spreadsheetId;

    //Populate Values
    $valuesData = (file_get_contents($requestPaths['values_batch'], FILE_USE_INCLUDE_PATH));
    $valuesData = str_replace("\r\n",'', $valuesData);
    $valuesData = json_decode($valuesData);
    foreach($valuesData->requests as $request) {
      $valueInputOption = $request->valueInputOption;
      $data = $request->data;
      $includeValuesInResponse = $request->includeValuesInResponse;
      $responseValueRenderOption = $request->responseValueRenderOption;

      $requestBody = new \Google_Service_Sheets_BatchUpdateValuesRequest();

      $requestBody->setData($request->data);
      $requestBody->setIncludeValuesInResponse($request->includeValuesInResponse);
      $requestBody->setResponseValueRenderOption($request->responseValueRenderOption);
      $requestBody->setValueInputOption($request->valueInputOption);

      $this->service->spreadsheets_values->batchUpdate($spreadsheetId, $requestBody);
    }
    unset($requestPaths['values_batch']);

    $requestArray = [];
    foreach($requestPaths as $requestsPath) {
      $requests = (file_get_contents($requestsPath, FILE_USE_INCLUDE_PATH));
      $requests = str_replace("\r\n",'', $requests);
      $requests = str_replace("sourceSheetId" , ''.$this->innerSpreadsheet->getProperties()->getSheetId() , $requests);
      $requests = json_decode($requests);

      foreach($requests->requests as $request) {
        $requestArray[] = $request;
      }

    }
    $batchUpdateRequest = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
      'requests' => $requestArray
    ]);

    $this->service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
  }

  /**
   * Set permissions for our google spreadsheet to be viewed/edited by anyone with a link.
   *
   * @return void
   */
  public function setGoogleSpreadsheetPermissions() {
    $driveService = new \Google_Service_Drive($this->client);
    $driveService->getClient()->setUseBatch(true);

    try {
        $batch = $driveService->createBatch();

        $userPermission = new \Google_Service_Drive_Permission(array(
            'type' => 'anyone',
            'role' => 'writer',
            'notify' => false
        ));
        $request = $driveService->permissions->create(
            $this->spreadsheetId, $userPermission, array('fields' => 'id'));
        $batch->add($request, 'user');
        $domainPermission = new \Google_Service_Drive_Permission(array(
            'type' => 'anyone',
            'role' => 'writer',
            'notify' => false
        ));
        $request = $driveService->permissions->create(
            $this->spreadsheetId, $domainPermission, array('fields' => 'id'));
        $batch->add($request, 'domain');
        $results = $batch->execute();

        foreach ($results as $result) {
            if ($result instanceof \Google_Service_Exception) {
                // Handle error
                printf($result);
            } else {
                //printf("Permission ID: %s\n", $result->id);
            }
        }
    } finally {
        $driveService->getClient()->setUseBatch(false);
    }
  }
}
 ?>

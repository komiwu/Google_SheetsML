@extends('layouts.app')

@section('content')
<h1 class="text-center">Google Sheets (PHP5.6)</h1>
<div class="container">
  <div class="card">
    <div class="card-header">Lets import a google sheet</div>
    <div class="card-body">
      Here we will use the Google Sheets API v4 to create, edit and share spreadsheets
      <br  /><br  />
      <iframe src="{{ rtrim($results->spreadsheetUrl) }}" height='600px' width='100%' scrolling="yes"></iframe>
      <br  /><br  />
      <pre>
        <?php //print_r($results);
        ?>
        spreadsheetId = <?php print $results->spreadsheetId ?>
      </pre>
    </div>
  </div>
  <div class="text-center">
    <button class="btn btn-primary center-block" type="button" id="refresh_rnd_vals_btn" onclick="refresh_random_values();">Refresh Random Values</button>
    &nbsp;&nbsp;
    <button class="btn btn-primary center-block" type="button" id="pop_btn" onclick="populateSpreadsheet();">Populate The Spreadsheet</button>
  </div>

  <br />
  <div class="text-center">
    <button class="btn btn-primary center-block" type="button" id="test_btn" onclick="test();">Testing Stuff!</button>
  </div>
  <br /><br />
  <pre id="responseText">
  </pre>
</div>

<script type="text/javascript">
//call to internal API to refresh all volatile functions
//only needs to send the spreadsheet id in data :{}
//return : null
function refresh_random_values() {
  $("#refresh_rnd_vals_btn").attr("disabled", true);
  console.log("we are in the startAjax function");
  $.ajax({
    type: "GET",
    url: "/api/Sheets_API/refreshSheetValues/<?php print $results->spreadsheetId ?>",
    async: true,
    cache: false,
    success: function(result) {
      $("#refresh_rnd_vals_btn").attr("disabled", false);
      console.log("success on ajax");
      console.log(result);
      //$("#responseText").html(result);
    },
    error: function(data, etype) {
      $("#refresh_rnd_vals_btn").attr("disabled", false);
      console.log("error on ajax");
      console.log(data);
      $("#responseText").html(data.responseText);
      console.log(etype);
    }
  });
}

//call to internval API to populate the spreadsheet. (values, formats, charts)
//only needs to send the spreadsheet id in data: {}
//return : null
function populateSpreadsheet() {
  $("#pop_btn").attr("disabled", true);
  console.log("we are in the populateSpreadsheet function");
  $.ajax({
    type: "GET",
    url: "/api/Sheets_API/populateSpeadsheet/<?php print $results->spreadsheetId ?>",
    async: true,
    cache: false,
    success: function(result) {
      $("#pop_btn").attr("disabled", false);
      console.log("success on ajax");
      console.log(result);
      //$("#responseText").html(result);
    },
    error: function(data, etype) {
      $("#pop_btn").attr("disabled", false);
      console.log("error on ajax");
      console.log(data);
      $("#responseText").html(data.responseText);
      console.log(etype);
    }
  });
}

//Test function for debugging
function test() {
  $("#test_btn").attr("disabled", true);
  console.log("we are in the test function");
  $.ajax({
    type: "GET",
    url: "/api/Sheets_API/test/<?php print $results->spreadsheetId ?>",
    async: true,
    cache: false,
    success: function(result) {
      $("#test_btn").attr("disabled", false);
      console.log("success on ajax");
      console.log(result);
      $("#responseText").html(result);
    },
    error: function(data, etype) {
      $("#test_btn").attr("disabled", false);
      console.log("error on ajax");
      console.log(data);
      $("#responseText").html(data.responseText);
      console.log(etype);
    }
  });
}
</script>
@endsection

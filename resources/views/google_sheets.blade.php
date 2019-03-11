@extends('layouts.app')

@section('content')
  <h1 class="text-center">Google Sheets (PHP5.6)</h1>
  <div class="container">
    <div class="card">
      <div class="card-header">Lets import a google sheet</div>
      <div class="card-body">
        Here we will use the Google Sheets API v4 to create, edit and share spreadsheets
        <br  /><br  />
        <iframe src="{{ rtrim($results->spreadsheetUrl) }}" height='1000px' width='100%' scrolling="yes"></iframe>
        <br  /><br  />
        <pre>
          <?php //print_r($results);
          ?>
          spreadsheetId = <?php print $results->spreadsheetId ?>
        </pre>
      </div>
    </div>
    <div class="text-center">
      <button class="btn btn-primary center-block" type="button" onclick="startAjax();">Refresh</button>
    </div>
    <br /><br />
    <pre id="responseText">
    </pre>
  </div>

<script type="text/javascript">
  function startAjax() {
    console.log("we are in the startAjax function");
    $.ajax({
      type: "GET",
      url: "/projects/google_sheets/refreshSheetValues/",
      async: false,
      cache: false,
      data: ({
        'spreadsheetId' : "<?php print $results->spreadsheetId ?>"
        <?php ?>
      }),
      success: function(result) {
        console.log("success on ajax");
        console.log(result);
        //$("#responseText").html(result);
      },
      error: function(data, etype) {
        console.log("error on ajax");
        console.log(data);
        $("#responseText").html(data.responseText);
        console.log(etype);
      }
    });
  }
</script>
@endsection

@extends('layouts.app')

@section('content')
  <h1>Google Sheets (PHP5.6)</h1>
  <div class="container">
    <div class="card">
      <div class="card-header">Lets import a google sheet</div>
      <div class="card-body">
        Here we will use the Google Sheets API v4 to create, edit and share spreadsheets
        <br  /><br  />
        <iframe src="{{ rtrim($results->spreadsheetUrl) }}" height='1000px' width='100%' scrolling="yes"></iframe>
        <br  /><br  />
        <?php var_dump($results); ?>
      </div>
    </div>
  </div>
@endsection

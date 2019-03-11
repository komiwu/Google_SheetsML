@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="card project-card bg-light mb-3 border-secondary" style="max-width: 18rem; display:inline-block">
      <img class="card-img-top" style="width: 18rem;" src="images\Google_API.png" alt="Card image cap">
      <div class="card-body text-secondary">
        <p class="card-text">In this project we will connect to Google's API (Google Sheet API and Google Drive API),
                            create various examples illustrating different features of the API's, and document our process.</p>
        <a href="/projects/google_sheets" class="btn btn-primary">View Project</a>
      </div>
    </div>

    <div class="card project-card bg-light mb-3 border-secondary" style="max-width: 18rem; display:inline-block">
      <img class="card-img-top" style="width: 18rem;" src="images\Google_API.png" alt="Card image cap">
      <div class="card-body text-secondary">
        <p class="card-text">CIS4900 Project page</p>
        <a href="/projects/google_sheets_CIS4900" class="btn btn-primary">View Project</a>
      </div>
    </div>
  </div>
@endsection

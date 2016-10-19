@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Add New Guide</div>

                <div class="panel-body">
                    <form class="form-horizontal" action = "{{ url('/add') }}" method="POST" enctype='multipart/form-data'>
    <div class="form-group">{{ csrf_field() }}
      <label class="control-label col-sm-2" style="text-align:left;">Title</label>
      <div class="col-sm-4">
        <input type="text" name="title" class="form-control input-sm" id="email" required>
      </div>
    </div>
    
    <div class="form-group">
      <label class="control-label col-sm-2" style="text-align:left;">Description</label>
      <div class="col-sm-8">
        <textarea class="form-control" rows="8" name="description" required></textarea>
      </div>
    </div>

    <div class="form-group">
      <label class="control-label col-sm-2" style="text-align:left;">Address</label>
      <div class="col-sm-6">
        <textarea class="form-control" rows="6" name="address" required></textarea>
      </div>
    </div>
 
	<div class="form-group">
                <label class="control-label col-sm-2" style="text-align:left;">Image</label>
		   <div class="col-sm-8">
                    <span class="btn btn-primary">
                        <input type="file" name="file">
                    </span>
                </div>
            </div>
	
	<div class="form-group">
      <label class="control-label col-sm-2" style="text-align:left;">Location</label>
      <div class="col-sm-3">
        <input type="text" placeholder="Latitude" name="lat" class="form-control input-sm" required>
      </div>
	<div class="col-sm-3">
        <input type="text" name="lng" placeholder="Longitude" class="form-control input-sm" required>
      </div>
    </div>	

	<div class="form-group">
      <label class="control-label col-sm-2" id="status" style="text-align:left;">Type</label>
      <div class="col-sm-3">
        <select class="form-control input-sm" name="type">
			<option value="1">Restaurants</option>
			<option value="2">Bars & Clubs</option>
			<option value="3">Hotels</option>
			<option value="4">Shopping</option>
		</select>
	  </div>
    </div>


    <div class="form-group">
      <div class="col-sm-offset-3 col-sm-10"><button type="button" class="btn btn-default btn-sm"><a href="{{url('/') }}" style="color:black; text-decoration:none;">Cancel</a></button>
       <button type="submit" class="btn btn-primary btn-sm">Add Guide</button>
      </div>
    </div>
  </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

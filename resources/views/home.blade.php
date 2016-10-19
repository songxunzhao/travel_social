@extends('layouts.app')
@section('addguide')
<li><a href="{{ url('/addguide') }}">Add New Guide</a></li>
<!--	<link type="text/css" href="/habbis/public/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link type="text/css" href="/habbis/public/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
	<link type="text/css" href="/habbis/public/css/theme.css" rel="stylesheet">
	<link type="text/css" href="/habbis/public/images/icons/css/font-awesome.css" rel="stylesheet">
	<link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>-->
@endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10">
           <div class="container">
					<div class="content">

						<div class="module">
							<div class="module-head">
							</div>
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            <h3 id="myModalLabel">Disable</h3>
                        </div>
			    <br/><br/>
                            <h4>    Are you sure you want to disable?</h4>
			    <br/><br/>
                        <div class="modal-footer">
                            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                            <button data-dismiss="modal" class="btn red" id="btnYes">Confirm</button>
                        </div>
   </div>

<div id="myModal1" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            <h3 id="myModalLabel">Disable</h3>
                        </div>
                            <br/><br/>
                            <h4>    Are you sure you want to delete this video?</h4>
                            <br/><br/>
                        <div class="modal-footer">
                            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
                            <button data-dismiss="modal" class="btn red" id="btnIm">Confirm</button>
                        </div>
   </div>
							<div class="module-body table">
								<table cellpadding="0" id="example" cellspacing="0" border="0" class="datatable-2 table table-bordered table-striped	 display" width="100%">
									<thead>
										<tr>
											<th>S.No</th>
											<th>Title</th>
											<th>Description</th>
											<th>Address</th>
											<th>Image</th>
											<th>Latitude</th>
											<th>Longitude</th>
											<th>Type</th>
										
										</tr>
									</thead>
									<tbody><?php $i =1; ?>
									@foreach ($guides as $guide)
										<tr id="doc_rows{{$i}}" class="odd gradeX">
											<td class="center">{{ $i }}</td>
											<td class="center">{{ $guide->title }}</td>
                                                                                        <td class="center">{{ $guide->description }}</td>
											<td class="center">{{ $guide->address }}</td>
											<td class="center"><img style="width:100px; height:100px;" src="{{ $guide->img }}" /></td>
											<td class="center">{{ $guide->lat }}</td>
											<td class="center">{{ $guide->lng }}</td>
											<td class="center"><?php if($guide->type ==1){ echo "Restaurants"; }elseif($guide->type ==2){echo "Bars & Clubs";}elseif($guide->type ==3){ echo "Hotels";}elseif($guide->type ==4){ echo "Shopping";}else{ echo "No type selected";} ?></td>
										</tr><?php $i++; ?>
									@endforeach

									</tbody>
								</table>
							</div>
						</div><!--/.module-->

					<br />
						
					</div><!--/.content-->
				</div><!--/.span9-->
    </div>
</div>
@endsection
@section('footer')
	<!--<script src="/habbis/public/scripts/jquery-1.9.1.min.js"></script>
	<script src="/habbis/public/scripts/jquery-ui-1.10.1.custom.min.js"></script>
	<script src="/habbis/public/bootstrap/js/bootstrap.min.js"></script>
	<script src="/habbis/public/scripts/datatables/jquery.dataTables.js"></script>
	--><script>
		$(document).ready(function() {
			$('.datatable-1').dataTable();
			$('.dataTables_paginate').addClass("btn-group datatable-pagination");
			$('.dataTables_paginate > a').wrapInner('<span />');
			$('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
			$('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');
		} );


function deleteClicked(id)
{
	console.log(id);
    $('#myModal').data('id', id).modal('show');
}
function deleteImage(id)
{
        console.log(id);
    $('#myModal1').data('id', id).modal('show');
}

$('#btnYes').click(function() {
    // handle deletion here
    var id = $('#myModal').data('id');
console.log("deleting id " + id);
$.ajax({
                    type: "POST",
                    url: "disable_user.php",
                    data: {item_type: 1,  s_aid: id},
                    dataType: "JSON",
                    success: function(result) {
//console.log('success');
location.reload();
                    },
                    error: function() {
//$('.container').load('sports.php', {type: sid}, function() {
//     $('#refresh_table').load('sports.php', {type: sid});
//                        alert('Error occured');
                    }
                });

console.log("id is " +id);
    $('[data-id='+id+']').parents('tr').remove();
    $('#myModal').modal('hide');
//   window.location.href += "?delete=" + id;
//window.location.reload(); 
});

$('#btnIm').click(function() {
    // handle deletion here
    var id = $('#myModal1').data('id');
console.log("deleting id " + id);
$.ajax({
                    type: "POST",
                    url: "disable_video.php",
                    data: {item_type: 1,  s_id: id},
                    dataType: "JSON",
                    success: function(result) {
console.log('success');
location.reload();
                    },
                    error: function() {
//$('.container').load('sports.php', {type: sid}, function() {
//     $('#refresh_table').load('sports.php', {type: sid});
//                        alert('Error occured');
                    }
                });

console.log("id is " +id);
    $('[data-id='+id+']').parents('tr').remove();
    $('#myModal1').modal('hide');
//   window.location.href += "?delete=" + id;
//window.location.reload();
});
	</script>
@endsection


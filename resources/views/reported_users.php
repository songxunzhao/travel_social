<!DOCTYPE html>
<?php
include ('session.php');
$sessionQuery = "select session from admin_pass";
$sessionResult = mysql_query($accQry, $db1->conn);
									
$row = mysql_fetch_assoc($sessionResult);
if ($row['session'] == $_SESSION["keys"]) {
} else {

//header("Location: login.php");
}

?>

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>MassCast Dashboard</title>
	<link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
	<link type="text/css" href="css/theme.css" rel="stylesheet">
	<link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
	<link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
</head>
<body>

	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".navbar-inverse-collapse">
					<i class="icon-reorder shaded"></i>
				</a>

			  	<a class="brand" href="#">
			  		MassCast
			  	</a>

			</div>
		</div><!-- /navbar-inner -->
	</div><!-- /navbar -->



	<div class="wrapper">
		<div class="container">
			<div class="row">
				<div class="span3">
					<div class="sidebar">



					</div><!--/.sidebar-->
				</div><!--/.span3-->


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
											<th>Email</th>
											<th>Videos</th>
											<th>Reason</th>
											<th>Block</th>
										</tr>
									</thead>
									<tbody>
<?php
								$accQry = "select distinct uid2 ru, (select group_concat(videos.url, '') from videos where videos.uid = ru) as image,(select group_concat(videos.vid, '') from videos where videos.uid = ru) as vid, (select email from user where uid = ru) as email,(select reason from report_user where uid2 = ru limit 1)as reason from report_user where state = 0";
									$result1 = mysql_query($accQry, $db1->conn);
									$i = 1;
									$month_array = array("1"=>"Jan", "2"=>"Feb", "3"=>"Mar", "4"=>"Apr", "5"=>"May", "6"=>"Jun", "7"=>"Jul", "8"=>"Aug", "9"=>"Sep", "10"=>"Oct", "11"=>"Nov", "12"=>"Dec");
								        while ($row = mysql_fetch_assoc($result1)) {

							                ?>

										<tr id="doc_rows<?php echo $i; ?>" class="odd gradeX">
											<td class="center"><?Php echo $i ?></td>
											<td class="center"><?Php echo $row['email'] ?></td>
                                                                                        <td class="center"><?Php if($row['image']==''){echo "No Videos";}else{ $pieces = explode(",", $row['image']);$vids = explode(",", $row['vid']);
													for ($j=0;$j<count($pieces);$j++) {
                                                                                                            ?><a target = '_blank' href="<?php echo $pieces[$j]; ?>"> Video<?php echo $j; ?></a>&nbsp;&nbsp;<a href="#" class="confirm-delete btn mini red-stripe" role="button"  onclick="deleteImage(<?php echo $vids[$j] ?>);">Delete</a><br/>
                                                                                        <?php }} ?></td>
											<td class="center"><?Php echo $row['reason'] ?></td>
											<td> <a href="#" class="confirm-delete btn mini red-stripe" role="button"  onclick="deleteClicked(<?Php echo $row['ru'] ?>);">Block</a> </td>
										</tr>
									 <?php
								                $i++;
									            }
								            ?>

									</tbody>
								</table>
							</div>
						</div><!--/.module-->

					<br />
						
					</div><!--/.content-->
				</div><!--/.span9-->
			</div>
		</div><!--/.container-->
	</div><!--/.wrapper-->


	<script src="scripts/jquery-1.9.1.min.js"></script>
	<script src="scripts/jquery-ui-1.10.1.custom.min.js"></script>
	<script src="bootstrap/js/bootstrap.min.js"></script>
	<script src="scripts/datatables/jquery.dataTables.js"></script>
	<script>
		$(document).ready(function() {
			$('.datatable-1').dataTable();
			$('.dataTables_paginate').addClass("btn-group datatable-pagination");
			$('.dataTables_paginate > a').wrapInner('<span />');
			$('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
			$('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');
		} );

/*		  $('#myModal').on('show', function() {
    var tit = $('.confirm-delete').data('title');

    $('#myModal .modal-body p').html("Desea eliminar al usuario " + '<b>' + tit +'</b>' + ' ?');
    var id = $(this).data('id'),
    removeBtn = $(this).find('.danger');
})
*/
/*$('.confirm-delete').on('click', function(e) {
 //   e.preventDefault();
console.log( $().jquery);
    var id = $(e).data('id');
	console.log("modal optioned for id " + id);
    $('#myModal').data('id', id).modal('show');
});
*/
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
</body>

</html>

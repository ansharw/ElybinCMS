<?php
/* Javascript
 * Module: -
 *	
 * Elybin CMS (www.elybin.com) - Open Source Content Management System 
 * @copyright	Copyright (C) 2014 - 2015 Elybin .Inc, All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Khakim Assidiqi <hamas182@gmail.com>
 */
@session_start();
if(empty($_SESSION['login'])){
	header('location: index.php');
}else{	
	@include_once('../../../elybin-core/elybin-function.php');
	@include_once('../../../elybin-core/elybin-oop.php');
	@include_once('../../lang/main.php');
	
	// get user privilages
	$tbus = new ElybinTable('elybin_users');
	$tbus = $tbus->SelectWhere('session',$_SESSION['login'],'','');
	$level = $tbus->current()->level; // getting level from curent user

	$tbug = new ElybinTable('elybin_usergroup');
	$tbug = $tbug->SelectWhere('usergroup_id',$level,'','');
	$usergroup = $tbug->current()->setting;

// give error if no have privilage
if($usergroup == 0){
	header('location:../403.html');
	exit;
}else{
	switch (@$_GET['act']) {
		case 'add': // case 'add'
?>
<!-- Optional javascripts -->
<script src="min/?b=assets/javascripts&amp;f=select2.min.js,bootstrap-datepicker.min.js,jquery.maskedinput.min.js"></script>
<!-- Javascript -->
<script>
init.push(function () {
	$('#tooltip a').tooltip();	
	$('#date-pick').datepicker();
	$("#date-input").mask("99/99/9999");		
		
	// on submit
	$('#form').submit(function(e){
		// disable button and growl!
		$('#form .btn-success').addClass('disabled');
		$.growl({ title: "<?php echo $lg_processing?>", message: "<?php echo $lg_processing?>...", duration: 9999*9999 });
		// start ajax
	    $.ajax({
	      url: $(this).attr('action'),
	      type: 'POST',
	      data: $(this).serialize(),
	      success: function(data) {
				// enable button
				$("#growls .growl-default").hide();
				$('#form .btn-success').removeClass('disabled');
	      		console.log(data);
				// decode json
				try {
					var data = $.parseJSON(data);
				}
				catch(e){
					// if failed to decode json
					$.growl.error({ title: "Failed to decode JSON!", message: e + "<br/><br/>" + data, duration: 10000 });
				}
				if(data.status == "ok"){
					// ok growl
					$.growl.notice({ title: data.title, message: data.isi });
					window.location.href="?mod=album";
				}
				else if(data.status == "error"){
					// error growl
					$.growl.warning({ title: data.title, message: data.isi });
				}
		   }
	    });
	    e.preventDefault();
	    return false;
  	});
});
</script>
<!-- / Javascript -->
<?php 
			break;

		case 'edit':
?>
<!-- Optional javascripts -->
<script src="min/?b=assets/javascripts&amp;f=select2.min.js,bootstrap-datepicker.min.js,jquery.maskedinput.min.js"></script>
<!-- Javascript -->
<script>
init.push(function () {
	$('#tooltip a').tooltip();	
	$('#date-pick').datepicker();
	$("#date-input").mask("99/99/9999");
	$('#switcher-style').switcher({
		theme: 'square',
		on_state_content: '<span class="fa fa-check"></span>',
		off_state_content: '<span class="fa fa-times"></span>'
	});

	// on submit
	$('#form').submit(function(e){
		// disable button and growl!
		$('#form .btn-success').addClass('disabled');
		$.growl({ title: "<?php echo $lg_processing?>", message: "<?php echo $lg_processing?>...", duration: 9999*9999 });
		// start ajax
	    $.ajax({
	      url: $(this).attr('action'),
	      type: 'POST',
	      data: $(this).serialize(),
	      success: function(data) {
				// enable button
				$("#growls .growl-default").hide();
				$('#form .btn-success').removeClass('disabled');
	      		console.log(data);
				// decode json
				try {
					var data = $.parseJSON(data);
				}
				catch(e){
					// if failed to decode json
					$.growl.error({ title: "Failed to decode JSON!", message: e + "<br/><br/>" + data, duration: 10000 });
				}
				if(data.status == "ok"){
					// ok growl
					$.growl.notice({ title: data.title, message: data.isi });
					window.location.href="?mod=album";
				}
				else if(data.status == "error"){
					// error growl
					$.growl.warning({ title: data.title, message: data.isi });
				}
		   }
	    });
	    e.preventDefault();
	    return false;
  	});
});
</script>
<script>  // endless scrolling
$(window).scroll(function() {
	if($(window).scrollTop() + $(window).height() == $(document).height()) {
		endless();
	}
});
$(document).ready(function () {
	$("#scroll-edge").click(function() {
		endless();
	});
});

// endless scroll
function endless(){
		// if $("#scroll-edge").attr("next") == true
		if($("#scroll-edge").attr("next") == 'true'){
			$("#scroll-edge").html('<img src="assets/images/plugins/bootstrap-editable/loading.gif"> Loading...');
			// get current start pos
			limit = $("#scroll-edge").attr("limit");
			aid = $("#scroll-edge").attr("aid");
			page = new Number($("#scroll-edge").attr("page"));
			// Get data
			$.getJSON("app/album/ajax/get_photos.php?page=" + page + "&limit=" + limit + '&aid=' + aid,function(result){
				$.each(result, function(i, f){
					console.log(f);
					// stop loading if data all showed
					if(f['next'] == false){
						$("#scroll-edge").attr("next", 'false').hide();
						console.log(f['next']);
					}else{
						$(".photos").append('
							<div class="box" id="box-' + f['data'] + '" ratio="' + f['ratio'] + '" style="height: 200px; width: ' + f['width'] + 'px">
								<input type="checkbox" name="image[]" id="for' + f['media_id'] + '" value="' + f['epm_media_id'] + '"' + f['chk'] + '>
								<label for="for' + f['media_id'] + '" class=" grid-gutter-margin-b">
									<i class="fa fa-check"></i>
									<img src="../file/m/' + f['hash'] + '" >
								</label>
							</div>
						');
					}
					
					// load more
					$("#scroll-edge").html('Load More...');
				});
			});
			console.log('Added ' + limit + 'row again, page ' + page);
			
			// change edge
			$("#scroll-edge").attr("page", page+1);
		    // $(".photos").rowGrid("appended");
		   
		}else{
			$("#scroll-edge").slideUp();
		}
		
};
</script>
<!-- / Javascript -->
<?php 
			break;	
			
	default: // case default
?>
<script type="text/javascript">
init.push(function () {
	$('#tooltip a, #tooltip-ck, #tooltip-foto').tooltip();
});
ElybinPager();
ElybinSearch();
ElybinCheckAll();
countDelData();
</script>
<?php		
			break;
	}
  }
}
?>
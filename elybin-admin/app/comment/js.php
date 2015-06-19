<?php
/* Javascript
 * Module: -
 *	
 * Elybin CMS (www.elybin.com) - Open Source Content Management System 
 * @copyright	Copyright (C) 2014 Elybin.Inc, All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Khakim Assidiqi <hamas182@gmail.com>
 */
@session_start();
if(empty($_SESSION['login'])){
	header('location:../../../403.php');
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
	header('location:../403.php');
	exit;
}else{
	switch (@$_GET['act']) {
		case 'add': // case 'add'

			break;

		case 'edit': // case 'edit'
?>
<!-- Optional javascripts -->
<!--
<script src="//cdnjs.cloudflare.com/ajax/libs/summernote/0.6.0/summernote.min.js"></script>
-->
<script src="assets/javascripts/summernote.min.js"></script>

<script type="text/javascript">
init.push(function () {
	$('#switcher-style').switcher({
		theme: 'square',
		on_state_content: '<span class="fa fa-check"></span>',
		off_state_content: '<span class="fa fa-times"></span>'
	});
	$('#tooltip a, #tooltipl').tooltip();

	$().ajaxStart(function() {
		$.growl({ title: "Loading", message: "Writing..." });
	}).ajaxStop(function() {
		$.growl({ title: "Success", message: "Success" });
	});

	//ajax
	$('#form').submit(function() {
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: $(this).serialize(),
			success: function(data) {
				data = explode(",",data);
				console.log(data);
				if(data[0] == "ok"){
					$.growl.notice({ title: data[1], message: data[2] });
					window.location.href="?mod=comment";
				}
				else if(data[0] == "error"){
					$.growl.warning({ title: data[1], message: data[2] });
				}
				

			}
		})
		return false;
	});

});
</script>
<?php
	// getting text_editor
	$tblo = new ElybinTable('elybin_options');
	$editor_id = $tblo->SelectWhere('name','text_editor','','');
	foreach ($editor_id as $op) {
		$editor = $op->value;
	}
	if($editor=='summernote'){
?>
<!-- Optional javascripts -->
<!--
<script src="//cdnjs.cloudflare.com/ajax/libs/summernote/0.6.0/summernote.min.js"></script>
-->
<script src="assets/javascripts/summernote.min.js"></script>
<script>
init.push(function () {
	//summernote editor
	if (! $('html').hasClass('ie8')) {
		$('#text-editor').summernote({
			height: 300,
			tabsize: 2,
			codemirror: {
				theme: 'monokai'
			},
			onImageUpload: function(files, editor, editable){
				uploadMedia(files[0], editor, editable);
			}
		});
	}
})
</script>
<?php 
	}
	elseif($editor=='bs-markdown'){
?>
<!--
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-markdown/2.2.1/js/bootstrap-markdown.min.js"></script>
-->
<script src="assets/javascripts/bootstrap-markdown.min.js"></script>
<script>
init.push(function () {
	if (! $('html').hasClass('ie8')) {
		$("#text-editor").markdown({ iconlibrary: 'fa' });
	}
})
</script>
<?php } ?>
<?php
			break;	
			
	default: // case default
?>
<script type="text/javascript">
init.push(function () {
	$('#tooltip a, #tooltipc, #tooltip-ck').tooltip();	
});

ElybinView();
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
<?php
/* Short description for file
 * [ Module: Post
 *	
 * Elybin CMS (www.elybin.com) - Open Source Content Management System 
 * @copyright	Copyright (C) 2014 - 2015 Elybin .Inc, All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Khakim Assidiqi <hamas182@gmail.com>
 */
if(!isset($_SESSION['login'])){
	header('location: index.php');
}else{
$modpath 	= "app/post/";
$action		= $modpath."proses.php";

// get user privilages
$tbus = new ElybinTable('elybin_users');
$tbus = $tbus->SelectWhere('session',$_SESSION['login'],'','');
$level = $tbus->current()->level; // getting level from curent user

$tbug = new ElybinTable('elybin_usergroup');
$tbug = $tbug->SelectWhere('usergroup_id',$level,'','');
$usergroup = $tbug->current()->post;


// give error if no have privilage
if($usergroup == 0){
	er('<strong>'.$lg_ouch.'!</strong> '.$lg_accessdenied.' 403 <a class="btn btn-default btn-xs pull-right" onClick="history.back();"><i class="fa fa-share"></i>&nbsp;'.$lg_back.'</a>');
	echo '';
}else{
	// start here
	$v 	= new ElybinValidasi();
	switch (@$_GET['act']) {
		case 'add':
?>
		<div class="page-header">
			<h1><span class="text-light-gray"><?php echo $lg_post?> / </span><?php echo $lg_addnew?></h1>
		</div> <!-- / .page-header -->
		<!-- Content here -->
		<div class="row">
			<div class="col-sm-12">
				<form class="panel form-horizontal" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
					<div class="panel-heading" id="tooltip">
						<span class="panel-title"><i class="fa fa-pencil"></i>&nbsp;&nbsp;<?php echo $lg_addnewpost?></span>
						<a class="btn btn-default btn-xs pull-right" data-toggle="modal" data-target="#help" data-placement="bottom" data-original-title="<?php echo $lg_help?>"><i class="fa fa-question-circle"></i></a>
					</div>
					<div class="panel-body">
						<div class="form-group">
					      <label class="col-sm-2 control-label"><?php echo $lg_title?>*</label>
					      <div class="col-sm-10">
					      	<input type="text" name="title" class="form-control" placeholder="<?php echo $lg_title?>"/>
					      </div>
						</div> <!-- / .form-group -->
						<div class="form-group">
					      <label class="col-sm-2 control-label"><?php echo $lg_content?>*</label>
					      <div class="col-sm-10">
<?php
	// getting text_editor
	$tblo = new ElybinTable('elybin_options');
	$editor = $tblo->SelectWhere('name','text_editor','','')->current()->value;
	if($editor=='summernote'){
?>
							<style><?php include("assets/stylesheets/summernote.css"); ?></style>
<?php 
	}
	elseif($editor=='bs-markdown'){
?>
							<style><?php include("assets/stylesheets/markdown.css"); ?></style>
<?php } ?>
							
					      	<div id="summernote-progress" style="display: none">
						      	<p>Uploading Images - <span>1%</span></p>
						      	<div class="progress progress-striped">
						      		<div class="progress-bar progress-bar-success" style="width: 1%"></div>
						      	</div>
						     </div>
					      	<textarea name="content" cols="50" rows="5" class="form-control" id="text-editor" placeholder="<?php echo $lg_content?>"></textarea>
					      </div>
						</div> <!-- / .form-group -->
						<div class="form-group">
					      <label class="col-sm-2 control-label"><?php echo $lg_category?></label>
					      <div class="col-sm-4">
							<style><?php include("assets/stylesheets/select2.min.css"); ?></style>
							<select name="category_id" id="multiselect-style">
					      	<?php
					      		$tbl = new ElybinTable('elybin_category');
					      		$cat = $tbl->SelectWhere('status','active','','');
					      		foreach($cat as $c){
					      	?>
								<option value="<?php echo $c->category_id; ?>"><?php echo $c->name; ?></option>
					      	<?php
					      		}
					      	?>
							</select>
					      </div>
						</div> <!-- / .form-group -->
						<div class="form-group">
					      <label class="col-sm-2 control-label"><?php echo $lg_tag?></label>
					      <div class="col-sm-4 select2-primary">
					      	<select name="tag[]" multiple="multiple" id="select-multiple" class="form-control">
					      	<?php
					      		$tbl = new ElybinTable('elybin_tag');
					      		$tag = $tbl->Select('tag_id','DESC');

					      		foreach($tag as $t){
					      	?>
					        	<option value="<?php echo $t->tag_id; ?>"><?php echo $t->name; ?></option>
					      	<?php
					      		}
					      	?>
					    	</select>
					      </div>
						</div> <!-- / .form-group -->
						<div class="form-group">
					      <label class="col-sm-2 control-label"><?php echo $lg_photo?></label>
					      <div class="col-sm-4">
					      	<input type="file" name="image" id="file-style" class="form-control"/>
					      	<p class="help-block"><?php echo $lg_photopostdesc?> (.jpg, .jpeg)</p>
					      </div>
						</div> <!-- / .form-group -->
						<div class="form-group">
					      <label class="col-sm-2 control-label"><?php echo $lg_status?></label>
					      <div class="col-sm-10">
					      	<input type="checkbox" name="status" class="form-control" id="switcher-style"/>
					      	<p class="help-block"><span class="fa fa-check"></span>&nbsp;<?php echo $lg_publish?>&nbsp;<span class="fa fa-times"></span>&nbsp;<?php echo $lg_draft?></p>
					      </div>
						</div> <!-- / .form-group -->
					  </div><!-- / .panel-body -->

					  <div class="panel-footer">
						  <button type="submit" value="Submit" class="btn btn-success"><i class="fa fa-check"></i>&nbsp;<?php echo $lg_savedata?></button>
						  <a class="btn btn-default pull-right" onClick="history.back();"><i class="fa fa-share"></i>&nbsp;<?php echo $lg_back?></a>
						  <input type="hidden" name="act" value="add" />
						  <input type="hidden" name="mod" value="post" />
					  </div> <!-- / .form-footer -->
				</form><!-- / .form -->

				<!-- Help modal -->
				<div id="help" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
								<h4 class="modal-title"><?php echo $lg_helptitle?></h4>
							</div>
							<div class="modal-body">...</div>
						</div> <!-- / .modal-content -->
					</div> <!-- / .modal-dialog -->
				</div> <!-- / .modal -->
				<!-- / Help modal -->
			</div><!-- / .col -->
		</div><!-- / .row -->
<?php

		break;

	case 'edit';
	$id 	= $v->sql(@$_GET['id']);
	$id 	= $v->xss($id);
	
	// check id exist or not
	$tb 	= new ElybinTable('elybin_posts');
	$copost = $tb->GetRow('post_id', $id);
	if(empty($id) OR ($copost == 0)){
		er('<strong>'.$lg_ouch.'!</strong> '.$lg_notfound.' 404<a class="btn btn-default btn-xs pull-right" onClick="history.back();"><i class="fa fa-share"></i>&nbsp;'.$lg_back.'</a>');
		theme_foot();
		exit;
	}
	
	// get data
	$cpost	= $tb->SelectWhere('post_id',$id,'','');
	$cpost	= $cpost->current();

	$content = html_entity_decode($cpost->content);
?>
		<div class="page-header">
			<h1><span class="text-light-gray"><?php echo $lg_post?> / </span><?php echo $lg_postedit?></h1>
		</div> <!-- / .page-header -->
		<!-- Content here -->
		<div class="row">
			<div class="col-sm-12">
				<form class="panel form-horizontal" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
					<div class="panel-heading" id="tooltip">
						<span class="panel-title"><i class="fa fa-pencil"></i>&nbsp;&nbsp;<?php echo $lg_editcurrentpost?></span>
						<a class="btn btn-default btn-xs pull-right" data-toggle="modal" data-target="#help" data-placement="bottom" data-original-title="<?php echo $lg_help?>"><i class="fa fa-question-circle"></i></a>
					</div>
					<div class="panel-body">
						<div class="form-group">
					      <label class="col-sm-2 control-label"><?php echo $lg_title?></label>
					      <div class="col-sm-10">
					      	<input type="text" name="title" value="<?php echo $cpost->title?>" class="form-control" placeholder="<?php echo $lg_title?>"/>
					      </div>
						</div> <!-- / .form-group -->
						
						
						<div class="form-group">
					      <label class="col-sm-2 control-label"><?php echo $lg_content?></label>
					      <div class="col-sm-10">
<?php
	// getting text_editor
	$tblo = new ElybinTable('elybin_options');
	$editor = $tblo->SelectWhere('name','text_editor','','')->current()->value;
	if($editor=='summernote'){
?>
							<style><?php include("assets/stylesheets/summernote.css"); ?></style>
<?php 
	}
	elseif($editor=='bs-markdown'){
?>
							<style><?php include("assets/stylesheets/markdown.css"); ?></style>
<?php } ?>
					      	<textarea name="content" cols="50" rows="5" class="form-control" id="text-editor" placeholder="<?php echo html_entity_decode($lg_content)?>"><?php echo $content?></textarea>
					      </div>
						</div> <!-- / .form-group -->
						<div class="form-group">
					      <label class="col-sm-2 control-label"><?php echo $lg_category?></label>
					      <div class="col-sm-4">
							<style><?php include("assets/stylesheets/select2.css"); ?></style>
							<select name="category_id" id="multiselect-style">
					      	<?php
					      		$tbl = new ElybinTable('elybin_category');
					      		$cat = $tbl->SelectWhere('status','active','','');
					      		foreach($cat as $c){
					      	?>
								<option value="<?php echo $c->category_id; ?>"<?php if($cpost->category_id==$c->category_id){echo ' selected=selected';}?>><?php echo $c->name; ?></option>
					      	<?php
					      		}
					      	?>
							</select>
					      </div>
						</div> <!-- / .form-group -->
						<div class="form-group">
					      <label class="col-sm-2 control-label"><?php echo $lg_tag?></label>
					      <div class="col-sm-4 select2-primary">
					      	<select name="tag[]" multiple="multiple" id="select-multiple" class="form-control">
					      	<?php
					      		$tbl = new ElybinTable('elybin_tag');
					      		$tag = $tbl->Select('tag_id','DESC');
					      		$afterexp = explode(',',$cpost->tag);
					      		foreach($tag as $t){
									$ck = (array_search($t->tag_id, $afterexp) === false)? '' : ' selected=selected';
					      	?>
					        <option value="<?php echo $t->tag_id?>"<?php echo $ck?>><?php echo $t->name?></option>
					      	<?php
					      		}
					      	?>
					    	</select>
					      </div>
						</div> <!-- / .form-group -->
						<div class="form-group">
					      <label class="col-sm-2 control-label"><?php echo $lg_photo?></label>
					      <?php
					      	if(!empty($cpost->image)){
					      ?>
					      <div class="col-sm-5">
					      	<img class="img-thumbnail" alt="<?php echo $lg_photo?>" src="../elybin-file/post/<?php echo $cpost->image?>"/>
					      </div>
					      <?php } ?>
					      <div class="col-sm-4">
					      	<input type="file" name="image" id="file-style" class="form-control"/>
					      	<p class="help-block"><?php echo $lg_leftphotoempty?></p>
					      </div>
						</div> <!-- / .form-group -->
						<div class="form-group">
					      <label class="col-sm-2 control-label"><?php echo $lg_allowcomment?></label>
					      <div class="col-sm-10">
					      	<input type="checkbox" name="comment" class="form-control" id="switcher-style" <?php if($cpost->comment=='allow'){echo 'checked="checked"';}?>/>
					      	<p class="help-block"><span class="fa fa-check"></span>&nbsp;<?php echo $lg_yes?>&nbsp;<span class="fa fa-times"></span>&nbsp;<?php echo $lg_no?></p>
					      </div>
						</div> <!-- / .form-group -->
						<div class="form-group">
					      <label class="col-sm-2 control-label"><?php echo $lg_status?></label>
					      <div class="col-sm-10">
					      	<input type="checkbox" name="status" class="form-control" id="switcher-style2" <?php if($cpost->status=='publish'){echo 'checked="checked"';}?>/>
					      	<p class="help-block"><span class="fa fa-check"></span>&nbsp;<?php echo $lg_publish?>&nbsp;<span class="fa fa-times"></span>&nbsp;<?php echo $lg_draft?></p>
					      </div>
						</div> <!-- / .form-group -->
					  </div><!-- / .panel-body -->

					  <div class="panel-footer">
						  <button type="submit" value="Submit" class="btn btn-success"><i class="fa fa-check"></i>&nbsp;<?php echo $lg_savechanges?></button>
						  <a class="btn btn-default pull-right" onClick="history.back();"><i class="fa fa-share"></i>&nbsp;<?php echo $lg_back?></a>
						  <input type="hidden" name="post_id" value="<?php echo $cpost->post_id; ?>" />
						  <input type="hidden" name="act" value="edit" />
						  <input type="hidden" name="mod" value="post" />
					  </div> <!-- / .form-footer -->
				</form><!-- / .form -->

				<!-- Help modal -->
				<div id="help" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
								<h4 class="modal-title"><?php echo $lg_helptitle?></h4>
							</div>
							<div class="modal-body">...</div>
						</div> <!-- / .modal-content -->
					</div> <!-- / .modal-dialog -->
				</div> <!-- / .modal -->
				<!-- / Help modal -->
			</div><!-- / .col -->
		</div><!-- / .row -->
<?php
		break;

	case 'editquick';
	$id 	= $v->sql($_GET['id']);
	$id 	= $v->xss($id);
	
	// check id exist or not
	$tb 	= new ElybinTable('elybin_posts');
	$copost = $tb->GetRow('post_id', $id);
	if(empty($id) OR ($copost == 0)){
		er('<strong>'.$lg_ouch.'!</strong> '.$lg_notfound.' 404<a class="btn btn-default btn-xs pull-right" onClick="history.back();"><i class="fa fa-share"></i>&nbsp;'.$lg_back.'</a>');
		theme_foot();
		exit;
	}
	
	$tb 	= new ElybinTable('elybin_posts');
	$cpost	= $tb->SelectWhere('post_id',$id,'','');
	$cpost	= $cpost->current();

	$content = html_entity_decode($cpost->content);
?>
							<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
							<h4 class="modal-title"><i class="fa fa-pencil"></i>&nbsp;&nbsp;<?php echo $lg_editcurrentpost?></h4>
							</div>
							<div class="modal-body">
								<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
									<div class="form-group">
								      <label class="col-sm-2 control-label"><?php echo $lg_title?></label>
								      <div class="col-sm-10">
								      	<input type="text" name="title" value="<?php echo $cpost->title?>" class="form-control" placeholder="<?php echo $lg_title?>"/>
								      </div>
									</div> <!-- / .form-group -->
									<div class="form-group">
								      <label class="col-sm-2 control-label"><?php echo $lg_content?></label>
								      <div class="col-sm-10">
<?php
	// getting text_editor
	$tblo = new ElybinTable('elybin_options');
	$editor = $tblo->SelectWhere('name','text_editor','','')->current()->value;
	if($editor=='summernote'){
?>
							<style><?php include("assets/stylesheets/summernote.css"); ?></style>
<?php 
	}
	elseif($editor=='bs-markdown'){
?>
							<style><?php include("assets/stylesheets/markdown.css"); ?></style>
<?php } ?>
								      	<textarea name="content" cols="50" rows="5" class="form-control" placeholder="<?php echo html_entity_decode($lg_content)?>"><?php echo $content?></textarea>
								      </div>
									</div> <!-- / .form-group -->
									<div class="form-group">
								      <label class="col-sm-2 control-label"><?php echo $lg_category?></label>
								      <div class="col-sm-4">
										<select name="category_id" id="multiselect-style" class="form-control">
								      	<?php
								      		$tbl = new ElybinTable('elybin_category');
								      		$cat = $tbl->SelectWhere('status','active','','');
								      		foreach($cat as $c){
								      	?>
											<option value="<?php echo $c->category_id; ?>"<?php if($cpost->category_id==$c->category_id){echo ' selected=selected';}?>><?php echo $c->name; ?></option>
								      	<?php
								      		}
								      	?>
										</select>
								      </div>
									</div> <!-- / .form-group -->

									<div class="form-group">
								      <label class="col-sm-2 control-label"><?php echo $lg_photo?></label>
								      <div class="col-sm-8">
								      	<input type="file" name="image" id="file-style" class="form-control"/>
								      	<p class="help-block"><?php echo $lg_leftphotoempty?></p>
								      </div>
									</div> <!-- / .form-group -->
									<hr></hr>
									<button type="submit" value="Submit" class="btn btn-success"><i class="fa fa-check"></i>&nbsp;<?php echo $lg_publish?></button>
									<a class="btn btn-default pull-right" data-dismiss="modal"><i class="fa fa-share"></i>&nbsp;<?php echo $lg_back?></a>
									<input type="hidden" name="post_id" value="<?php echo $cpost->post_id?>" />
									<input type="hidden" name="comment" value="<?php echo $cpost->comment?>" />
									<input type="hidden" name="status" value="on" />
									<input type="hidden" name="act" value="edit" />
									<input type="hidden" name="mod" value="post" />
								</form><!-- / .form -->
							</div>
			
<?php
		break;

	case 'del':
?>

							<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
							<h4 class="modal-title"><i class="fa fa-exclamation-circle"></i>&nbsp;&nbsp;<?php echo $lg_deletetitle?></h4>
							</div>
							<div class="modal-body">
								<?php echo $lg_deletequestion?><br/><br/>
								<i><?php echo $lg_relatedcommentwillbedeleted ?></i>
								<hr/>
								<form action="<?php echo $action?>" method="post">
									<button type="submit" class="btn btn-danger"><i class="fa fa-check"></i>&nbsp;<?php echo $lg_yesdelete?></button>
									<a class="btn btn-default pull-right" data-dismiss="modal"><i class="fa fa-share"></i>&nbsp;<?php echo $lg_cancel?></a>
									<input type="hidden" name="post_id" value="<?php echo $_GET['id']?>" />
									<input type="hidden" name="act" value="del" />
									<input type="hidden" name="mod" value="post" />
								</form>
							</div>
<?php
		break;
	
	default:
	$tb 	= new ElybinTable('elybin_posts');
	$lpost	= $tb->Select('post_id','DESC');
	$no = 1;
	
	$getoption1 = new ElybinTable('elybin_options'); 
	$homeurl = $getoption1->SelectWhere('name','site_url','','')->current()->value; 
?>	
		<!-- Page Header -->
		<div class="page-header">
			<div class="row">
				<h1 class="col-xs-12 col-sm-6 col-md-6 text-center text-left-sm">
					<span class="hidden-sm hidden-md hidden-lg"><i class="fa fa-pencil"></i>&nbsp;&nbsp;<?php echo $lg_post?></span>
					<span class="hidden-xs"><span class="text-light-gray"><?php echo $lg_post?> / </span><?php echo $lg_all?></span>
				</h1>
				<div class="col-xs-12 col-sm-6 col-md-6">
					<div class="row">
						<hr class="visible-xs no-grid-gutter-h">
						<div class="pull-right col-xs-12 col-sm-6 col-md-4">	
							<a href="?mod=<?php echo @$_GET['mod']?>&amp;act=add" class="pull-right btn btn-success btn-labeled" style="width: 100%">
							<span class="btn-label icon fa fa-plus"></span>&nbsp;&nbsp;<?php echo $lg_addnew?></a>
						</div>
						<!-- Margin -->
						<div class="visible-xs clearfix form-group-margin"></div>
						<!-- Search Bar -->
						<form action="#" class="pull-right col-xs-12 col-sm-6 col-md-8">
							<div class="input-group no-margin">
								<span class="input-group-addon" style="border:none;background: #fff;background: rgba(0,0,0,.05);"><i class="fa fa-search"></i></span>
								<input id="search" placeholder="<?php echo $lg_search?>..." class="form-control no-padding-hr" style="border:none;background: #fff;background: rgba(0,0,0,.05);" type="text">
							</div>
						</form>
					</div>
				</div>
			</div>
		</div> <!-- ./Page Header -->
		
		<!-- Content here -->
		<div class="row">
			<div class="col-sm-12">
				<form action="<?php echo $action?>" method="post" class="panel">
					<input type="hidden" name="act" value="multidel" />
					<input type="hidden" name="mod" value="post" />
					
					<!-- Panel Heading -->
					<div class="panel-heading">
						<span class="panel-title"><i class="fa fa-pencil hidden-xs">&nbsp;&nbsp;</i><?php echo $lg_allpost?></span>
						<div class="panel-heading-controls" id="tooltip">
							<a class="btn btn-default btn-xs" data-toggle="modal" data-target="#help" data-placement="bottom" data-original-title="<?php echo $lg_help?>"><i class="fa fa-question-circle"></i></a>
						</div> <!-- / .panel-heading-controls -->
					</div> 
					<!-- ./Panel Heading -->
					
					<div class="panel-body">
					  <div class="table-responsive">
						<table class="table table-hover" id="results">
						 <thead>
						   <tr>
						    <th>#</th>
						    <th><i class="fa fa-check-square" id="tooltip-ck" data-placement="bottom" data-toggle="tooltip" data-original-title="<?php echo $lg_checkall?>"></i></th>
						    <th><?php echo $lg_title?></th>
						    <th><?php echo $lg_writer?></th>
						    <th><?php echo $lg_category?></th>
						    <th><span class="fa fa-comment" id="tooltipc" data-placement="bottom" data-toggle="tooltip" data-original-title="<?php echo $lg_totalcomment?>"></span></th>
						    <th><?php echo $lg_date?></th>
						    <th><?php echo $lg_status?></th>
						    <th><?php echo $lg_action?></th>
						   </tr>
						 </thead>
						 <tbody>
						<?php
						
						foreach($lpost as $ps){
							$tbc 	= new ElybinTable('elybin_comments');
							$count	= $tbc->GetRow('post_id',$ps->post_id);

							$tba 	= new ElybinTable('elybin_users');
							$tba	= $tba->SelectWhere('user_id',$ps->author,'','');
							foreach($tba as $tba){ $author = $tba->fullname; }

							$tbcat 	= new ElybinTable('elybin_category');
							$tbcat	= $tbcat->SelectWhere('category_id',$ps->category_id,'','');
							foreach($tbcat as $tbcat){ $category = $tbcat->name; }
						?>
						   <tr>
							<td><?php echo $no?></td>
							<td><label class="px-single"><input type="checkbox" class="px" name="del[]" value="<?php echo $ps->post_id?>|<?php echo $ps->title?>"><span class="lbl"></span></label></td>
							<td><?php echo $ps->title?></td>
							<td><?php echo $author?></td>
							<td><?php echo $category?></td>
							<td><?php echo $count?></td>
							<td><?php echo $ps->date?></td>
							<td><?php echo $ps->status?></td>
							<td>
								<div id="tooltip">
									<a href="?mod=post&amp;act=edit&amp;id=<?php echo $ps->post_id?>" class="btn btn-success btn-outline btn-sm" data-placement="bottom" data-toggle="tooltip" data-original-title="<?php echo $lg_edit?>"><i class="fa fa-pencil-square-o"></i></a>
						    		<a href="<?php echo $homeurl; ?>/post-<?php echo $ps->post_id; ?>-<?php echo $ps->seotitle; ?>.html" target="_blank" class="btn btn-success btn-outline btn-sm<?php if($ps->status == 'draft'){ echo ' disabled'; }?>" data-placement="bottom" data-toggle="tooltip" data-original-title="<?php echo $lg_view?>" target="_blank"><i class="fa <?php if($ps->status == 'draft'){ echo 'fa-lock'; }else{ echo 'fa-desktop';}?>"></i></a>
						    		<a href="?mod=post&amp;act=del&amp;id=<?php echo $ps->post_id?>&amp;clear=yes" class="btn btn-danger btn-outline btn-sm" data-toggle="modal" data-target="#delete"  data-placement="bottom" data-original-title="<?php echo $lg_delete?>"><i class="fa fa-times"></i></a>
								</div>
							</td>
						   </tr>
						<?php
							$no++;
						}
						?>
						 </tbody>
						</table>
					  </div> <!-- /.table-responsive -->
						<div class="alert" id="notfound"><strong><?php echo $lg_nodatafound?></strong></div>
						<hr/>
						<!-- Multi Delete Modal -->
						<div id="deleteall" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
							<div class="modal-dialog modal-sm">
								<div class="modal-content">
									<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
									<h4 class="modal-title"><i class="fa fa-exclamation-circle"></i>&nbsp;&nbsp;<?php echo $lg_deletetitle?></h4>
									</div>
									<div class="modal-body">
										<?php echo $lg_deletequestion?>
										<div id="deltext"></div>
										<hr/>
										<button type="submit" class="btn btn-danger"><i class="fa fa-check"></i>&nbsp;<?php echo $lg_yesdelete?></button>
										<a class="btn btn-default pull-right" data-dismiss="modal"><i class="fa fa-share"></i>&nbsp;<?php echo $lg_cancel?></a>
									</div>
								</div> <!-- / .modal-content -->
							</div> <!-- / .modal-dialog -->
						</div> <!-- / .modal -->
						<!-- / Multi Delete Modal -->
						<div class="col-md-3">
							<button class="btn btn-danger btn-sm" id="delall" data-toggle="modal" data-target="#deleteall"><i class="fa fa-times"></i>&nbsp;&nbsp;<?php echo $lg_deleteselected?></button>
						</div>
						<div class="col-md-4 col-md-offset-5 text-right">
							<ul class="pagination pagination-xs" id="page-nav">
							</ul>
						</div>
					</div><!-- / .panel-body -->
				</form>
				<!-- Delete Modal -->
				<div id="delete" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
					<div class="modal-dialog modal-sm">
						<div class="modal-content">
							<?php echo $lg_loading?>...
						</div> <!-- / .modal-content -->
					</div> <!-- / .modal-dialog -->
				</div> <!-- / .modal -->
				<!-- / Delete Modal -->
				<!-- Help modal -->
				<div id="help" class="modal fade" tabindex="-1" role="dialog" style="display: none;">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
								<h4 class="modal-title"><?php echo $lg_helptitle?></h4>
							</div>
							<div class="modal-body">
								...
							</div>
						</div> <!-- / .modal-content -->
					</div> <!-- / .modal-dialog -->
				</div> <!-- / .modal -->
				<!-- / Help modal -->
			</div><!-- / .col -->
		</div><!-- / .row -->
<?php
	break;
		}

	}
}
?>
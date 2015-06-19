<?php
/* Short description for file
 * [ Module: Album - Manage album of Photos
 *	
 * Elybin CMS (www.elybin.com) - Open Source Content Management System 
 * @copyright	Copyright (C) 2014 - 2015 Elybin .Inc, All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Khakim Assidiqi <hamas182@gmail.com>
 */
if(!isset($_SESSION['login'])){
	// new redirect
	header('location: ../../../403.html');
	exit;
}else{
// declare first
$modpath 	= "app/album/";
$action		= $modpath."proses.php";

// string validation for security
$v 	= new ElybinValidasi();

// get usergroup privilage/access from current user to this module
$usergroup = _ug()->album;

// give error if no have privilage
if($usergroup == 0){
	er('<strong>'.lg('Ouch!').'</strong> '.lg('You don\'t have access to access this page. Access Desied 403.').'<a class="btn btn-default btn-xs pull-right" onClick="history.back();"><i class="fa fa-share"></i>&nbsp;'.lg('Back').'</a>');
	theme_foot();
	exit;
}else{
	// start here
	switch (@$_GET['act']) {
		case 'add':
		// buat auto draf
		$tbl = new ElybinTable('elybin_posts');
		$date = date("Y-m-d H:i:s");
		$data = array(
			'title' => '',
			'content' => '',
			'date' => $date,
			'author' => _u()->user_id,
			'category_id' => 0,
			'seotitle' => '',
			'tag' => '',
			'status' => 'prepost',					
			'visibility' => '',
			'post_meta' => '',
			'post_password' => '',
			'comment' => '',
			'type' => 'album'
		);
		$tbl->Insert($data);
		// ambil id ini
		$ca = $tbl->SelectWhereAnd('type', 'album', 'date', $date, 'post_id', 'DESC')->current();
?>		<!-- help -->
		<div class="page-header hide-light" id="help-panel">
			<p><?php echo lg('...') ?></p>
		</div>
		<!-- breadcrumb -->
		<ul class="breadcrumb breadcrumb-page">
			<li><a href="?mod=home"><?php echo lg('Home') ?></a></li>
			<li><a href="?mod=album"><?php echo lg('Album') ?></a></li>
			<li class="active"><a href="?mod=album&amp;act=add"><?php echo lg('Add Album') ?></a></li>
			
			<div class="pull-right">
				<a class="btn btn-xs" id="help-button"><i class="fa fa-question-circle"></i> <?php echo lg('Help') ?></a>
			</div>
		</ul>
		<!-- Content here -->
		<div class="page-header">
			<a href="?mod=media" class="btn btn-default pull-right"><i class="fa fa-long-arrow-left"></i>&nbsp;&nbsp;<?php echo lg('Back to Album List') ?></a>
			<h1><?php echo lg('Add New Album') ?></h1>
		</div> <!-- / .page-header -->
		
				
		<form action="<?php echo $action ?>" method="post" id="form">
			<div class="row">
				<div class="col-sm-12">
					<div class="form-horizontal panel-wide depth-panel">
						<div class="panel-body">
							<div class="form-group">
							  <div class="col-sm-12 col-md-8">
								<input type="text" name="name" class="form-control input-lg" placeholder="<?php echo lg('Type the Album Title')?>" required/>
							  </div>
							  <div class="col-sm-12">
								<p class="help-block"><?php echo lg('Tips: Best to use title of album that not too short or too long and not using much of numerical and symbol character.')?></p>
								<hr/>
								<?php
								// show images
								$tbm = new ElybinTable('elybin_media');
								$lm = $tbm->SelectWhere('type','image');
								//show 
								foreach($lm as $cm){
								?>
									 <div class="col-sm-12 col-md-4">
										<img src="../file/m/<?php echo $cm->hash ?>" class=" grid-gutter-margin-b" style="max-width: 100%">
										<input type="checkbox" name="image[]" value="<?php echo epm_encode($cm->media_id) ?>">
									 </div>
								<?php
								}
								?>
							  </div>
							  <p class="depth-sm text-center col-md-12 panel-padding">
							  <?php echo lg('Load More...')?>
							  </p>
							</div> <!-- / .form-group -->
						</div>	
						<div class="panel-footer">
							<button type="submit" value="Submit" class="btn btn-success"><i class="fa fa-check"></i>&nbsp;<?php echo lg('Save Data')?></button>
							<a class="btn btn-default pull-right" onClick="history.back();"><i class="fa fa-share"></i>&nbsp;<?php echo lg('Back')?></a>
							<input type="hidden" name="aid" value="<?php echo epm_encode($ca->post_id) ?>" />
							<input type="hidden" name="act" value="add" />
							<input type="hidden" name="mod" value="album" />
						</div> <!-- / .form-footer -->
					</div>
				</div><!-- / .col -->
			</div>
		</form>
<?php
			break;

		case 'edit':
		$hash 	= $v->sql(epm_decode(@$_GET['hash']));
		
		// check id exist or not
		$tbp 	= new ElybinTable('elybin_posts');
		$coalbum = $tbp->GetRowAnd('post_id', $hash,'type','album');
		if($coalbum < 1){
			er('<strong>'.lg('Ouch!').'</strong> '.lg('Page Not Found 404.').'<a class="btn btn-default btn-xs pull-right" onClick="history.back();"><i class="fa fa-share"></i>&nbsp;'.lg('Back').'</a>');
			theme_foot();
			exit;
		}
		
		// get data
		$ca	= $tbp->SelectWhereAnd('post_id', $hash,'type','album')->current();
?>
		<!-- help -->
		<div class="page-header hide-light" id="help-panel">
			<p><?php echo lg('...') ?></p>
		</div>
		<!-- breadcrumb -->
		<ul class="breadcrumb breadcrumb-page">
			<li><a href="?mod=home"><?php echo lg('Home') ?></a></li>
			<li><a href="?mod=album"><?php echo lg('Album') ?></a></li>
			<li class="active"><a href="?mod=album&amp;act=edit"><?php echo lg('Edit Album') ?></a></li>
			
			<div class="pull-right">
				<a class="btn btn-xs" id="help-button"><i class="fa fa-question-circle"></i> <?php echo lg('Help') ?></a>
			</div>
		</ul>
		<!-- Content here -->
		<div class="page-header">
			<a href="?mod=album" class="btn btn-default pull-right"><i class="fa fa-long-arrow-left"></i>&nbsp;&nbsp;<?php echo lg('Back to Album List') ?></a>
			<h1><?php echo lg('Edit Album') ?></h1>
		</div> <!-- / .page-header -->
		
				
		<form action="<?php echo $action ?>" method="post" id="form">
			<div class="row">
				<div class="col-sm-12">
					<div class="form-horizontal panel-wide depth-panel">
						<div class="panel-body">
							<div class="form-group">
							  <div class="col-sm-12 col-md-10">
								<input type="text" name="name" class="form-control input-lg" placeholder="<?php echo lg('Type the Album Title')?>" value="<?php echo $ca->title ?>" required/>
							  </div>
							  <div class="visible-xs clearfix form-group-margin"></div>
							  <div class="col-sm-12 col-md-2">
								<button type="submit" value="Submit" class="btn btn-success btn-lg" width="100%"><i class="fa fa-check"></i>&nbsp;<?php echo lg('Save Chenges')?></button>
							  </div>
							  <div class="col-sm-12">
								<style>
.photos {
}
/* clearfix */
.photos:before,
.photos:after {
    content: "";
    display: table;
}
.photos:after {
    clear: both;
}

.box {
  float: left;
  margin-bottom: 10px; 
  margin-right: 10px; 
  postition: relative;
  background-color: #eee;
  
  background-image: url('assets/images/plugins/bootstrap-editable/loading.gif');
  background-position: 50% 50%;
  background-repeat: no-repeat;
}
.box label img {
  max-width: 100%;
  max-height: 100%;
  vertical-align: bottom;
}
.first-item {
  clear: both;
}
/* remove margin bottom on last row */
.last-row, .last-row ~ .box {
  margin-bottom: 0;
}							.box input[type=checkbox]{
										display: none;
									}
									.box label i{
										position: absolute;
										color: #fff;
										margin: 15px 25px;
										font-size: 0px;
										transition:0.08s
									}
									.box label{
										margin: 0px;
									}
									.box input[type=checkbox] + label{
background: #3498DB;
padding: 0px;
transition:0.05s
}
									.box input[type=checkbox]:checked + label{
padding: 0px;
transition:0.03s
									}
									.box input[type=checkbox]:checked + label i{
display: block;
padding:10px;
background-color:#3498DB;
border-radius: 100px;
font-size: 21px;
transition:0.06s
								</style>
							  </div>
							  <div class="col-sm-12">
								<p class="help-block"><?php echo lg('Tips: Best to use title of album that not too short or too long and not using much of numerical and symbol character.')?></p>
								<hr/>
								<div class="photos">
								<?php
								// show images
								$tbm = new ElybinTable('elybin_media');
								$lm = $tbm->SelectFullCustom("
								SELECT 
								* 
								FROM  
								`elybin_media` AS  `m` 
								LEFT JOIN  
								`elybin_relation` AS  `r` 
								ON  
								`r`.`second_id` =  `m`.`media_id` 
								WHERE  
								`m`.`type` =  'image'
								GROUP BY 
								`m`.`media_id`
								ORDER BY 
								`r`.`rel_id` DESC,  
								`r`.`second_id` DESC,
								`m`.`media_id` DESC
								LIMIT 0,15
								");
								//show 
								foreach($lm as $cm){
									// checked
									$tbr = new ElybinTable('elybin_relation');
									$cor = $tbr->GetRowAnd('first_id', $ca->post_id, 'second_id', $cm->media_id);
									if($cor > 0){
										$chk = ' checked="checked"';
									}else{
										$chk = '';
									}
									// get resolution
									if(@json_decode($cm->metadata) !== false){
										$metadata = @json_decode($cm->metadata);
										//var_dump(json_decode($cm->metadata)->COMPUTED->Height );
									}else{
										@$metadata->COMPUTED->Height = 1;
										@$metadata->COMPUTED->Width = 1;
									}
									
									$ratio = $metadata->COMPUTED->Height/$metadata->COMPUTED->Width;
									$width = $metadata->COMPUTED->Width/$metadata->COMPUTED->Height*200;
								?>	
									 <div class="box" id="box-<?php echo $cm->hash ?>" ratio="<?php echo $ratio?>" style="height: 200px; width: <?php echo $width?>px">
										<input type="checkbox" name="image[]" id="for<?php echo $cm->media_id ?>" value="<?php echo epm_encode($cm->media_id) ?>"<?php echo $chk ?>>
										<label for="for<?php echo $cm->media_id ?>" class=" grid-gutter-margin-b">
											<i class="fa fa-check"></i>
											<img src="../file/m/<?php echo $cm->hash ?>" >
										</label>
									 </div>
								<?php
								}
								?>
								</div>
							  <a class="depth-sm text-center col-md-12 panel-padding" id="scroll-edge" limit="15" aid="<?php echo $ca->post_id ?>" page="2" next="true">
							  <?php echo lg('Load More...')?>
							  </a>
							  </div>
							</div> <!-- / .form-group -->
							
							<input type="hidden" name="aid" value="<?php echo epm_encode($ca->post_id) ?>" />
							<input type="hidden" name="act" value="add" />
							<input type="hidden" name="mod" value="album" />
						</div>	
					</div>
				</div><!-- / .col -->
			</div>
		</form>
<?php
			break;

		case 'del':
?>
							<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
							<h4 class="modal-title"><i class="fa fa-exclamation-circle"></i>&nbsp;&nbsp;<?php echo $lg_deletetitle?></h4>
							</div>
							<div class="modal-body">
								<?php echo $lg_deletequestion?>
								<hr></hr>
								<form action="<?php echo $action?>" method="post">
									<button type="submit" class="btn btn-danger"><i class="fa fa-check"></i>&nbsp;<?php echo $lg_yesdelete?></button>
									<a class="btn btn-default pull-right" data-dismiss="modal"><i class="fa fa-share"></i>&nbsp;<?php echo $lg_cancel?></a>
									<input type="hidden" name="album_id" value="<?php echo $_GET['id']?>" />
									<input type="hidden" name="act" value="del" />
									<input type="hidden" name="mod" value="album" />
								</form>
							</div>
<?php
			break;
		
	default:
		$tba 	= new ElybinTable('elybin_posts');
		$lalbum	= $tba->SelectFullCustom("
			SELECT
			*
			FROM
			`elybin_posts` as `p`
			WHERE 
			`p`.`type` = 'album' &&
			`status` = 'published'
		");
		$calbum	= $tba->GetRow();
		
		$search = $v->sql(@$_GET['search']);
?>		<!-- help -->
		<div class="page-header" id="help-panel" style="display: none">
			<p><?php echo lg('...') ?></p>
		</div>
		<!-- breadcrumb -->
		<ul class="breadcrumb breadcrumb-page">
			<div class="breadcrumb-label text-light-gray"><?php echo lg('You are here:') ?></div>
			<li><a href="?mod=home"><?php echo lg('Home') ?></a></li>
			<li class="active"><a href="?mod=album"><?php echo lg('Album') ?></a></li>
			
			<div class="pull-right">
				<a class="btn btn-xs" id="help-button"><i class="fa fa-question-circle"></i> <?php echo lg('Help') ?></a>
			</div>
		</ul>
		<!-- Page Header -->
		<div class="page-header">
			<div class="row">
				<h1 class="col-xs-12 col-sm-6 col-md-6 text-center text-left-sm">
					<span class="hidden-sm hidden-md hidden-lg"><i class="fa fa-picture-o"></i>&nbsp;&nbsp;<?php echo lg('Album')?></span>
					<span class="hidden-xs"><?php echo lg('Album') ?></span>
					<?php if($search!==''){ echo '&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-light-gray text-sm">'.lg('Search result for').' <i>&#34;'.$search.'&#34;</i>';} ?>
				</h1>
				<div class="col-xs-12 col-sm-6 col-md-6">
					<div class="row">
						<hr class="visible-xs no-grid-gutter-h">
						<div class="pull-right col-xs-12 col-sm-6 col-md-5">	
							<a href="?mod=album&amp;act=add" class="pull-right btn btn-success btn-labeled" style="width: 100%">
							<span class="btn-label icon fa fa-plus"></span>&nbsp;&nbsp;<?php echo lg('New Album')?></a>
						</div>

					</div>
				</div>
			</div>
		</div> <!-- ./Page Header -->
	
		<!-- Content here -->
		<div class="row">
			<div class="col-sm-12">	
				<!-- Tabs -->
				<ul class="nav nav-tabs nav-tabs-xs">
					<li<?php if(!isset($_GET['filter'])){echo' class="active"'; }?>>
						<?php 
						// count all category
						$totallt = $tba->GetRowFullCustom("
							SELECT
							*
							FROM
							`elybin_posts` as `p`
							WHERE 
							`p`.`type` = 'album'
						");
						?>
						<a href="?mod=tag"><?php echo lg('All') ?> <span class="badge badge-default"><?php echo $totallt ?></span></a>
					</li>
				</ul> <!-- / .nav -->
				<!-- Panel -->
				<div class="panel">
					<!-- ./Panel Heading -->
					<div class="panel-body">
						<?php
						if($calbum == 0){
							echo '<div class="text-center text-light-gray panel-padding"><i class="fa fa-5x fa-tags"></i><br/>'.lg('You don\'t have any tag!').'</div>';
						
						}else{

							foreach($lalbum as $ca){
								// show images
								$cm = $tba->GetRowFullCustom("
								SELECT 
								*
								FROM
								`elybin_relation` as `r`,
								`elybin_media` as `m`
								WHERE
								`m`.`media_id` = `r`.`second_id` &&
								`r`.`first_id` = ".$ca->post_id."
								");
								// show images
								$lm = $tba->SelectFullCustom("
								SELECT 
								*
								FROM
								`elybin_relation` as `r`,
								`elybin_media` as `m`
								WHERE
								`m`.`media_id` = `r`.`second_id` &&
								`r`.`first_id` = ".$ca->post_id."
								ORDER BY `m`.`media_id` DESC 
								LIMIT 0,4
								");
								?>
								<div class="col-sm-12">
									<div class="row">
										<h2 style="  margin-top: 0px; margin-bottom: -12px; margin-left: 8px;"><?php echo $ca->title ?></h2>
										<a href="?mod=album&amp;act=edit&amp;hash=<?php echo epm_encode($ca->post_id) ?>" class="btn btn-sm btn-default pull-right"><i class="fa fa-pencil"></i> <?php echo lg('Edit') ?></a>
										<br/>
										<p class="text-light-gray" style="margin-left: 10px;"><?php echo $cm ?> <?php echo lg('Photos') ?></p>
									</div>
									<div class="row">
								<?php
								//show 
								foreach($lm as $cm){
								?>
									 <div class="col-sm-12 col-md-3">
										<img src="../file/s/<?php echo $cm->hash ?>" class=" grid-gutter-margin-b" style="max-width: 100%">
									 </div>
								<?php
								}
								echo '
									</div>
								</div>';
							}
							echo '
							<hr/>';
						
						}
						?>
					</div><!-- / .panel-body -->
				</div><!-- / .panel -->
			</div><!-- / .col -->
		</div><!-- / .row -->
<?php
		break;
		}	
	}
}
?>

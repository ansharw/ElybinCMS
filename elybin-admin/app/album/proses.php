<?php
/* Short description for file
 * [ Module: Album Proccess
 *	
 * Elybin CMS (www.elybin.com) - Open Source Content Management System 
 * @copyright	Copyright (C) 2014 - 2015 Elybin .Inc, All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Khakim Assidiqi <hamas182@gmail.com>
 */
session_start();
if(empty($_SESSION['login'])){
	header('location:../../../403.php');
}else{	
	include_once('../../../elybin-core/elybin-function.php');
	include_once('../../../elybin-core/elybin-oop.php');
	include_once('../../lang/main.php');

	// string validation for security
	$v 	= new ElybinValidasi();

	// get usergroup privilage/access from current user to this module
	$usergroup = _ug()->album;

// give error if no have privilage
if($usergroup == 0){
	header('location:../../../403.php');
}else{
	// start here
	$mod = $_POST['mod'];
	$act = $_POST['act'];

	//ADD
	if ($mod=='album' AND $act=='add'){
		$aid = $v->sql(epm_decode($_POST['aid']));
		$name = $v->xss($_POST['name']);
		$image = $_POST['image'];

		//if field empty
		if(empty($name)){
			$name = lg('Untitled');
		}
		
		// basic info
 		$tbp = new ElybinTable('elybin_posts');
		$d1 = array(
			'title' => $name,
			'seotitle' => seo_title($name)
			); 
		$tbp->Update($d1,'post_id', $aid);
		
		// relate images to their album
		$tbr = new ElybinTable('elybin_relation');
		foreach($image as $iid){
			$d2 = array(
				'type' => 'album',
				'target' => 'media',
				'first_id' => $aid,
				'second_id' => $v->sql(epm_decode($iid))
			);
			$tbr->Insert($d2);
		}
		
		//Done
		$a = array(
			'status' => 'ok',
			'title' => $lg_success,
			'isi' => $lg_datainputsuccessful
		);
		echo json_encode($a);
	}
	//EDIT
	elseif($mod=='album' AND $act=='edit'){
		$album_id = $v->sql($_POST['album_id']);
		$name = $v->xss($_POST['name']);
		$date = $v->xss($_POST['date']);
		$status = @$_POST['status'];
		if($status == "on"){
			$status = "active";
		}else{
			$status = "deactive";
		}
		
		// check id exist or not
		$tbl 	= new ElybinTable('elybin_album');
		$coalbum = $tbl->GetRow('album_id', $album_id);
		if(empty($album_id) OR ($coalbum == 0)){
			$a = array(
				'status' => 'error',
				'title' => $lg_error,
				'isi' => $lg_iderrorpleasereloadpage
			);

			echo json_encode($a);
			exit;
		}
		
		//if field empty
		if(empty($date) || empty($album_id)){
			//echo "{,$lg_pleasefillimportant}";
			$a = array(
				'status' => 'error',
				'title' => $lg_error,
				'isi' => $lg_pleasefillimportant
			);

			echo json_encode($a);
			exit;
		}

		if(empty($name)){
			$name = "($lg_untitled)";
		}

		$date = explode("/", $date); //mm-dd-yyyy
		$date = $date['2']."-".$date['0']."-".$date[1]; //yyyy-mm-dd
		$seotitle =  seo_title($name);


		$data = array(
			'name' => $name,
			'seotitle' => $seotitle,
			'date' => $date,
			'status' => $status	
			);
		$tbl->Update($data,'album_id',$album_id);
		//header('location:../../admin.php?mod='.$mod);

		$a = array(
			'status' => 'ok',
			'title' => $lg_success,
			'isi' => $lg_datainputsuccessful
		);
		echo json_encode($a);
	}

	//DEL
	elseif($mod=='album' AND $act=='del'){
		$album_id = $v->sql($_POST['album_id']);
		$tablegal = new ElybinTable('elybin_gallery'); //del foto
		
		// check id exist or not
		$tabledel = new ElybinTable('elybin_album');//del album
		$coalbum = $tabledel->GetRow('album_id', $album_id);
		if(empty($album_id) OR ($coalbum == 0)){
			header('location: ../../../404.html');
			exit;
		}
		
		$cimg = $tablegal->SelectWhere('album_id',$album_id,'','');
		foreach($cimg as $i){
			$image = $i->image;
			$fileimage = "../../../elybin-file/gallery/$image";
				if (file_exists("$fileimage")){
					unlink("../../../elybin-file/gallery/$image");
					unlink("../../../elybin-file/gallery/medium-$image");
				}
			$tablegal->Delete('gallery_id', $gallery_id); //del foto
		}

		
		$tabledel->Delete('album_id', $album_id); 

		$tbgal= new ElybinTable('elybin_gallery');
		$delgal = $tbgal->Delete('album_id',$album_id);
		header('location:../../admin.php?mod='.$mod);
	}
	//MULTI DEL
	elseif($mod=='album' AND $act=='multidel'){
		$album_id = $_POST['del'];

		if(!empty($album_id)){
			foreach ($album_id as $tg) {
				$pecah = explode("|",$tg);
				$pecah = $pecah[0];
				$album_id_fix = $v->sql($pecah);
				
				// check id exist or not
				$tabledel = new ElybinTable('elybin_album');//del album
				$coalbum = $tabledel->GetRow('album_id', $album_id_fix);
				if(empty($album_id_fix) OR ($coalbum == 0)){
					header('location: ../../../404.html');
					exit;
				}
		
				$tablegal = new ElybinTable('elybin_gallery'); //del foto
				$cimg = $tablegal->SelectWhere('album_id',$album_id_fix,'','');
				foreach($cimg as $i){
					$image = $i->image;
					$fileimage = "../../../elybin-file/gallery/$image";
						if (file_exists("$fileimage")){
							unlink("../../../elybin-file/gallery/$image");
							unlink("../../../elybin-file/gallery/medium-$image");
						}
					$tablegal->Delete('gallery_id', $gallery_id); //del foto
				}

				//del album
				$tabledel->Delete('album_id', $album_id_fix); 

				$tbgal= new ElybinTable('elybin_gallery');
				$delgal = $tbgal->Delete('album_id',$album_id_fix);
				header('location:../../admin.php?mod='.$mod);
			}
		} // > foreach
	}	
	//404
	else{
		header('location: ../../../404.php');
	}
}	
}
?>
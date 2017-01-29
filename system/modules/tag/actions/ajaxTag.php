<?php
/**
 * updates or removes tag item 
 *
 * @author Robert Lockerbie, robert@lockerbie.id.au, 2015
 **/
function ajaxTag_ALL(Web $w) {
	$id = $w->request("id");
	$class=$w->request("class");
	$user = $w->Auth->user();
	$cmd=$w->request("cmd");
	if( !$user->hasRole("tag_user") && !$user->hasRole("tag_admin") ) {
		echo 'Invalid request';
		return;
	}
	if( !empty($id) && !empty($class) && !empty($user) && !empty($cmd) ){
		if( 'get' == $cmd ) {
			$tags = $w->Tag->getTagsByObject($id, $class);
			$tagArray = array();
			if (!empty($tags)) {
				foreach($tags as $tagO) {
					$tag = array(
						'id' => $tagO->id,
						'value' => $tagO->id,
						'tag' => $tagO->tag,
						'label' => $tagO->tag,
						'tag_color' => $tagO->tag_color,
					);
					$tagArray[$tagO->id] = $tag;
				}
			}
			header('Content-type: application/json;');
			echo json_encode($tagArray);
		} else if( 'getAll' == $cmd ) {
			//Build list of all available tags
			$tags = $w->Tag->getAllTags();
			$tagArray = array();
			if (!empty($tags)) {
				foreach($tags as $tagA) {
					$tag = array(
						'id' => $tagA['id'],
						'value' => $tagA['id'],
						'tag' => $tagA['tag'],
						'label' => $tagA['tag'],
						'tag_color' => $tagA['tag_color'],
					);
					$tagArray[] = $tag;
				}
			}
			header('Content-type: application/json;');
			echo json_encode($tagArray);
		} else if ( 'setTag' == $cmd ) {
			//Load tag from DB
			$tag = $w->Tag->getTag($w->request('tagId'));
			if(!empty($tag)) {
				$w->Tag->addTag($id, $class, $tag->tag, $tag->tag_color);
			}
		} else if ( 'removeTag' == $cmd ) {
			$tag = $w->Tag->getTag($w->request('tagId'));
			if(!empty($tag)) {
				//We don't remove tag by id but by object class/id and tag name
				$w->Tag->removeTag($id, $class, $tag->tag);
			}
		} else if ( 'addTag' == $cmd ) {
			//Only tag admin can add tags...
			if( $user->hasRole("tag_admin") ) {
				$w->Tag->addTag($id, $class, $w->request('tag'));
			} else {
				echo "Invalid request";
			}
		} else {
			echo "Invalid request";
		}
	} else {
		echo "Invalid request";
	}
}

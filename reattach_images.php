<?php

/*
Plugin Name: Reattach Images Between Pages
Plugin URI: http://madething.org/post/1529181499/solved-moving-image-attachments-between-pages-in
Description: This plugin adds a drop-down menu to the Media > Edit screen for detaching and reattaching images between pages. This allows you to move an existing image to a new page without uploading it all over again.
Version: 1
Author: Johnathon Williams
Author URI: http://oddjar.com/ 
*/

/**
 * Copyright (c) 2010 Your Name. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

	

/**
 *
 * @param array $form_fields
 * @param object $post
 * @return array
 */
function my_image_attachment_fields_to_edit($form_fields, $post) {
	// only activate for images that already attached to pages, ignore images attached to posts
	if (get_post_type($post->post_parent) == 'page') {
		// get the list of pages for our select box
		$all_pages = get_pages();
		$select_code = get_pages_as_select_field($post, $all_pages);
		// $form_fields is a special array of fields to include in the attachment form
		// $post is the attachment record in the database
		// $post->post_type == 'attachment'
		// (attachments are treated as posts in WordPress)
		// add our custom field to the $form_fields array
		// input type="text" name/id="attachments[$attachment->ID][custom1]"
		$form_fields["post_parent"] = array(
			"label" => __("Attatched to page"),
			"input" => "html", 
			"html" => $select_code
		);
	}
	return $form_fields;
}

/**
 *
 * @param object $post
 * @param object $all_pages
 * @return string
 */
function get_pages_as_select_field($post, $all_pages) {

		$content = "<select name='attachments[{$post->ID}][post_parent]' id='attachments[{$post->ID}][post_parent]'>";
		foreach ($all_pages as $page) {
			if ($page->ID == $post->post_parent) {
				$selected = ' SELECTED ';
			} else {
				$selected = ' ';
			}
			$option_line = "<option" . $selected . "value='" . $page->ID . "'>" . $page->post_title . "</option>";
			$content = $content . $option_line;
		}		
		$content = $content . "</select>";
		return $content;
}

// attach our function to the correct hook
add_filter("attachment_fields_to_edit", "my_image_attachment_fields_to_edit", null, 2);

/**
 * @param array $post
 * @param array $attachment
 * @return array
 */
function my_image_attachment_fields_to_save($post, $attachment) {
	if( isset($attachment['post_parent']) ){
		if( trim($attachment['post_parent']) == '' ){
			// adding our custom error
			$post['errors']['post_parent']['errors'][] = __('No value found for post_parent.');
		}else{
			$post['post_parent'] = $attachment['post_parent'];
		}
	}
	return $post;
}
add_filter("attachment_fields_to_save", "my_image_attachment_fields_to_save", null, 2);
?>
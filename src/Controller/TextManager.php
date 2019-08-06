<?php

namespace Drupal\heritage_text_manager\Controller;

/**
* Simple page controller for drupal.
*/
class TextManager {
	/**
	* {@inheritdoc} Return title of the Edit Source Page
	*/
	public function getTitle($sourceid = NULL) {
		$connection = \Drupal::database();
		$field_title = '';
		$source_info = $connection->query("SELECT field_name, nid FROM `heritage_field_meta_data` WHERE id = :sourceid", array(':sourceid' => $sourceid))->fetchAll();
		$field_values = explode('_', $source_info[0]->field_name);
		$field_id = $field_values[2];
		$field_title = $connection->query("SELECT title FROM `heritage_source_info` WHERE id = :field_id", array(':field_id' => $field_id))->fetchField();
		$title = "Editing ".$field_title;
		return $title;
	}
}

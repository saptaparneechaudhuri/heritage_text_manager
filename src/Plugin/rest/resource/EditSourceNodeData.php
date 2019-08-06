<?php

namespace Drupal\heritage_text_manager\Plugin\rest\resource;

use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;

/**
 * Provides a resource to edit a source node data.
 *
 * @RestResource(
 *   id = "edit_source_node_data",
 *   label = @Translation("Edit Source Node Data"),
 *   uri_paths = {
 *     "canonical" = "/api/{field_name}/{nid}/edit",
	   "https://www.drupal.org/link-relations/create" = "/api/{field_name}/{nid}/edit"
 *   }
 * )
 */
class EditSourceNodeData extends ResourceBase {

  /**
   * Responds to POST requests for editing source nodes.
   *
   * @param sourceid
   *   Unique ID of the source
   *
   * @return \Drupal\rest\ModifiedResourceResponse
   *   The HTTP response object.
   */
	public function post($field_name = NULL, $nid = NULL, $arg) {
		$source_data = explode("_", $field_name);
		$sourceid = $source_data[2];
		$connection = \Drupal::database();
		$source_info = $connection->query("SELECT field_name, nid FROM `heritage_field_meta_data` WHERE nid = :nid AND field_name = :field_name", array(':field_name' => $field_name, ':nid' => $nid))->fetchAll();
		$info_present = count($source_info);
		if($info_present > 0){
			if(!isset($arg['body'])){
				$message = [
					'success' => 0,
					'message' => 'required parameters missing'
				];
				$statuscode = 400;
			}
			else{
				$node = \Drupal\node\Entity\Node::load($source_info[0]->nid);
				$node->{$source_info[0]->field_name}->value = $arg['body'];
				$node->save();
				$message = [
					'success' => 1,
					'message' => 'source updated'
				];
				$statuscode = 200;
			}
		}
		else{
			$message = [
				'success' => 0,
				'message' => 'page not found'
			];
			$statuscode = 404;
		}
		return new ModifiedResourceResponse($message, $statuscode);
	}	
}

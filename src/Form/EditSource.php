<?php 
namespace Drupal\heritage_text_manager\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class EditSource extends FormBase {
	/**
	* {@inheritdoc}
	*/
	public function getFormId() {    
		return 'heritage_edit_source';
	}
	/**
	* {@inheritdoc}
	*/
	public function buildForm(array $form, FormStateInterface $form_state, $sourceid = NULL) {
		$connection = \Drupal::database();
		$source_info = $connection->query("SELECT field_name, nid FROM `heritage_field_meta_data` WHERE id = :sourceid", array(':sourceid' => $sourceid))->fetchAll();
		$node = \Drupal\node\Entity\Node::load($source_info[0]->nid);
		$form['body'] = array(
			'#type' => 'text_format',
			'#title' => $this->t('Content'),
			'#default_value' => $node->{$source_info[0]->field_name}->value,
			'#format'=> 'full_html',
		);	
		$form['sourceid'] = array(
			'#type' => 'hidden',
			'#value' => $sourceid,
		);
		$form['nid'] = array(
			'#type' => 'hidden',
			'#value' => $source_info[0]->nid,
		);
		$form['actions']['submit'] = array(
			'#type' => 'submit',
			'#value' => $this->t('Save Heritage Source'),
		);
		return $form;
	}

	/**
	* {@inheritdoc} Stores the newly added text schema into the database table `heritage_text_structure` and creates the content types and vocabulary
	*/
	public function submitForm(array &$form, FormStateInterface $form_state) {
		$nid = $form_state->getValue('nid');
		$body = $form_state->getValue('body');
		$source_id = $form_state->getValue('sourceid');
		$node = \Drupal\node\Entity\Node::load($nid);
		$connection = \Drupal::database();
		$field_name = $connection->query("SELECT field_name FROM `heritage_field_meta_data` WHERE nid = :nid AND id = :sourceid", array(':nid' => $nid, ':sourceid' => $source_id))->fetchField();
		$node->{$field_name}->value = $body['value'];
		$node->save();
		$form_state->setRedirect('entity.node.canonical', array('node' => $nid));
	}
}


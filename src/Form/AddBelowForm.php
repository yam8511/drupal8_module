<?php

namespace Drupal\zoular\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\zoular\RateStorage;
use Drupal\zoular\RelationStorage;

/**
 * Class RateForm.
 *
 * @package Drupal\zoular\Form
 */
class AddBelowForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'add_below_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['up'] = [
      '#type' => 'email',
      '#title' => $this->t('上層Email(若空白，預設為自己)'),
    ];
    $form['down'] = [
      '#type' => 'email',
      '#title' => $this->t('下層Email'),
      '#required' => true
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Save',
    ];

    return $form;
  }

  /**
    * {@inheritdoc}
    */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
      
      $down = $form_state->getValue('down');
      $up = $form_state->getValue('up');

      $down_id = RelationStorage::getUser(['init' => $down])->uid;
      $up_id = RelationStorage::getUser(['init' => $up])->uid;
      if (!$up) {
        $up_id = $this->currentUser()->id();
      }

      //die('up: '.$up_id. ' _ '.$up . ', down: '.$down_id. ' _ '.$down);

      if (!$up_id || !$down_id) {
        drupal_set_message(t('設定失敗'));
      }
      else {
        // Save the submitted entry.
        $entry = array(
          'uid' => $down_id,
          'up' => $up_id
        );      
        RelationStorage::insert($entry);
        drupal_set_message(t('設定成功'));
      }
  }

}

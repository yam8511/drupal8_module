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
class EditBelowForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'edit_below_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $uid = NULL) {
  	$info = ['bg' => 0, 'sg' => 0, 'bb' => 0, 'sb' => 0];
    #嘗試取得資料庫資料
    $result = RateStorage::getRate($uid);
    if ($result) {
      $info['bg'] = $result->bg;
      $info['sg'] = $result->sg;
      $info['bb'] = $result->bb;
      $info['sb'] = $result->sb;
    }

    $father_rate = RateStorage::getFatherRate($uid);
    $max = 999999999;

    $form['uid'] = [
    	 '#type' => 'hidden',
    	 '#default_value' => $uid,
    ];

    $form['bg'] = [
      '#type' => 'number',
      '#title' => $this->t('單場大賠率'),
      '#default_value' => $info['bg'],
      '#min' => 0,
      '#max' => !$father_rate ? $max : $father_rate->bg,
    ];
    $form['sg'] = [
      '#type' => 'number',
      '#title' => $this->t('單場小賠率'),
      '#default_value' => $info['sg'],
      '#min' => 0,
      '#max' => !$father_rate ? $max : $father_rate->sg,
    ];
    $form['bb'] = [
      '#type' => 'number',
      '#title' => $this->t('單注大賠率'),
      '#default_value' => $info['bb'],
      '#min' => 0,
      '#max' => !$father_rate ? $max : $father_rate->bb,
    ];
    $form['sb'] = [
      '#type' => 'number',
      '#title' => $this->t('單注小賠率'),
      '#default_value' => $info['sb'],
      '#min' => 0,
      '#max' => !$father_rate ? $max : $father_rate->sb,
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Save',
    ];
    $form['actions']['delete'] = [
      '#type' => 'submit',
      '#value' => 'Delete',
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
    // Gather the current user so the new record has ownership.
    $uid = $form_state->getValue('uid');
    
    if ($form_state->getValue('op') === 'Save') {
      // Save the submitted entry.
      $entry = array(
        'uid' => $uid,
        'name' => RelationStorage::getName($uid),
        'bg' => $form_state->getValue('bg'),
        'sg' => $form_state->getValue('sg'),
        'bb' => $form_state->getValue('bb'),
        'sb' => $form_state->getValue('sb'),
      );

      RateStorage::update($entry);
      drupal_set_message(t('修改成功'));
    }
    elseif ($form_state->getValue('op') === 'Delete') {
      RelationStorage::delete(['uid' => $uid]);
      drupal_set_message(t('刪除成功'));
    }
  }

}

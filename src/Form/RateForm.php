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
class RateForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rate_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $info = ['bg' => 0, 'sg' => 0, 'bb' => 0, 'sb' => 0];
    #嘗試取得資料庫資料
    $user = $this->currentUser();
    $result = RateStorage::getRate($user->id());
    if ($result) {
      $info['bg'] = $result->bg;
      $info['sg'] = $result->sg;
      $info['bb'] = $result->bb;
      $info['sb'] = $result->sb;
    }

    $father_rate = RateStorage::getFatherRate($user->id());
    $max = 999999999;

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
      '#value' => '儲存',
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
    $account = $this->currentUser();
    // Save the submitted entry.
    $entry = array(
      'uid' => $account->id(),
      'name' => $account->getUsername(),
      'bg' => $form_state->getValue('bg'),
      'sg' => $form_state->getValue('sg'),
      'bb' => $form_state->getValue('bb'),
      'sb' => $form_state->getValue('sb'),
    );

    RateStorage::update($entry);
    drupal_set_message(t('修改成功'));
  }

}

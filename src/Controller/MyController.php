<?php

namespace Drupal\zoular\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\zoular\RateStorage;
use Drupal\zoular\RelationStorage;

/**
 * Class MyController.
 *
 * @package Drupal\zoular\Controller
 */
class MyController extends ControllerBase {

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public function hello($name) {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: hello with parameter(s): ').$name,
    ];
  }

  /**
   * Description
   * @return string
   *   Return Description string
   */
  public function description() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('This is my module for user and rate setting.'),
    ];
  }

  /**
   * Seek
   * @return string
   *   Return Seek string
   */
  public function seek() {
    $content = array();
    $user = $this->currentUser();

    $content['message'] = array(
      '#markup' => $user->getUsername().$this->t(' :查看自己層級以下的帳戶'),
    );

    $rows = array();
    $headers = array(t('name'), t('單場大賠率'), t('單場小賠率'), t('單注大賠率'), t('單注小賠率'));
    $result = RelationStorage::load(['up' => $user->id()]);

    for ($i=0; $i < count($result) ; $i++) { 
      $rates = RateStorage::getRate($result[$i]->uid);
      $rows[$i][0] = $rates->name;
      $rows[$i][1] = $rates->bg;
      $rows[$i][2] = $rates->sg;
      $rows[$i][3] = $rates->bb;
      $rows[$i][4] = $rates->sb;
    }
    $content['table'] = array(
      '#type' => 'table',
      '#header' => $headers,
      '#rows' => $rows,
      '#empty' => t('No entries available.'),
    );
    // Don't cache this page.
    $content['#cache']['max-age'] = 0;

    return $content;
  }
  
}

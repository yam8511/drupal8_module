<?php

namespace Drupal\zoular\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\zoular\RateStorage;
use Drupal\zoular\RelationStorage;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
    $content = array();
    $user = $this->currentUser();

    $content['message'] = array(
      '#markup' => $user->getUsername().$this->t(' :查看賠率限額'),
    );

    $rows = array();
    $headers = array(t('單場大賠率'), t('單場小賠率'), t('單注大賠率'), t('單注小賠率'));
    $rates = RateStorage::getRate($user->id());
    $rows[0][0] = $rates->bg;
    $rows[0][1] = $rates->sg;
    $rows[0][2] = $rates->bb;
    $rows[0][3] = $rates->sb;


    $content['table'] = array(
      '#type' => 'table',
      '#header' => $headers,
      '#rows' => $rows,
      '#empty' => t('目前沒有下屬會員'),
    );
    // Don't cache this page.
    $content['#cache']['max-age'] = 0;

    return $content;
  }

  /**
   * Seek
   * @return string
   *   Return Seek string
   */
  public function seek() {
    $user = $this->currentUser();
    return $this->getBelowView($user->id());
  }

  /**
   * Look for
   * @return string
   *   Return Look for string
   */
  public function lookfor($uid) {
    $user = $this->currentUser();
    if (!RelationStorage::isUpper($uid, $user->id())) {
      $link = Url::fromRoute('zoular.my_controller_seek')->toString();
      return RedirectResponse::create($link);
    }

    return $this->getBelowView($uid);
  }

  private function getBelowView($uid) {
    $username = RelationStorage::getName($uid);
    $user = $this->currentUser();
    if ($uid != $user->id()) {
      $father = RelationStorage::father($uid);

      if ($father != $user->id())
        $username = Link::createFromRoute($username, 'zoular.my_controller_lookfor', ['uid' => $father])->toString();
      else
        $username = Link::createFromRoute($username, 'zoular.my_controller_seek')->toString();
    }

    $content = array();
    $content['message'] = array(
      '#markup' => $username.$this->t(' :查看自己層級以下的帳戶'),
    );

    $content['table'] = array(
      '#type' => 'table',
      '#header' => array(t('name'), t('單場大賠率'), t('單場小賠率'), t('單注大賠率'), t('單注小賠率'), t('設定賠率')),
      '#rows' => $this->getBelowRate($uid),
      '#empty' => t('目前沒有下屬會員'),
    );
    // Don't cache this page.
    $content['#cache']['max-age'] = 0;
    return $content;
  }

  private function getBelowRate($uid) {
    $rows = [];
    $result = RelationStorage::load(['up' => $uid]);
    foreach ($result as $i => $account) {
      $rows[$i] = $this->getPersonRate($account->uid);
    }
    return $rows;
  }

  private function getPersonRate($uid) {
    $rows = [];
    $rates = RateStorage::getRate($uid);
    $name = RelationStorage::getName($uid);
    $rows[0] = Link::createFromRoute($name, 'zoular.my_controller_lookfor', ['uid' => $uid])->toString();
    $rows[1] = $rates->bg;
    $rows[2] = $rates->sg;
    $rows[3] = $rates->bb;
    $rows[4] = $rates->sb;
    $rows[5] = Link::createFromRoute($name, 'zoular.rate_below_form', ['uid' => $uid])->toString();
    return $rows;
  }

  public function getRateForm($uid) {
    $form = \Drupal::formBuilder()->getForm('Drupal\zoular\Form\EditBelowForm', $uid);
    return $form;
  }
  
}

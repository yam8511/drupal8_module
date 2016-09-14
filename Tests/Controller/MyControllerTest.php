<?php

namespace Drupal\zoular\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the zoular module.
 */
class MyControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => "zoular MyController's controller functionality",
      'description' => 'Test Unit for module zoular and controller MyController.',
      'group' => 'Other',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests zoular functionality.
   */
  public function testMyController() {
    // Check that the basic functions of module zoular.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}

<?php

namespace Drupal\zoular;

/**
 * Class ZoularStorage.
 */
class RelationStorage {

  /**
   * Save an entry in the database.
   *
   * The underlying DBTNG function is db_insert().
   *
   * Exception handling is shown in this example. It could be simplified
   * without the try/catch blocks, but since an insert will throw an exception
   * and terminate your application if the exception is not handled, it is best
   * to employ try/catch.
   *
   * @param array $entry
   *   An array containing all the fields of the database record.
   *
   * @return int
   *   The number of updated rows.
   *
   * @throws \Exception
   *   When the database insert fails.
   *
   * @see db_insert()
   */
  public static function insert($entry) {
    $return_value = NULL;
    try {
      $return_value = db_insert('zoular_relation')
        ->fields($entry)
        ->execute();
    }
    catch (\Exception $e) {
      drupal_set_message(t('db_insert failed. Message = %message, query= %query', array(
        '%message' => $e->getMessage(),
        '%query' => $e->query_string,
      )
      ), 'error');
    }
    return $return_value;
  }

  public static function delete($entry) {
    db_delete('zoular_relation')
        ->condition('uid', $entry['uid'])
        ->execute();
  }

  public static function load($entry = array()) {
    // Read all fields from the zoular_relation table.
    $select = db_select('zoular_relation', 'example');
    $select->fields('example');

    // Add each field and value as a condition to this query.
    foreach ($entry as $field => $value) {
      $select->condition($field, $value);
    }
    // Return the result in object format.
    return $select->execute()->fetchAll();
  }


  public static function father($id) {
    if (!$id) {
      return null;
    }

    $table = db_select('zoular_relation', 'example');
    $table->fields('example');
    $table->condition('uid', $id);
    $result = $table->execute()->fetch();
    return $result->up;
  }

  public static function getName($id) {
    if (!$id) {
      return null;
    }

    $table = db_select('users_field_data', 'example');
    $table->fields('example');
    $table->condition('uid', $id);
    $result = $table->execute()->fetch();
    return $result->name;
  }

  public static function getUser($entry = array()) {
    // Read all fields from the zoular_relation table.
    $select = db_select('users_field_data', 'example');
    $select->fields('example');

    // Add each field and value as a condition to this query.
    foreach ($entry as $field => $value) {
      $select->condition($field, $value);
    }
    // Return the result in object format.
    return $select->execute()->fetch();
  }

  public static function isUpper($id, $parent_id) {
    // Look for Upper
    $table = db_select('zoular_relation', 'example');
    $table->fields('example');
    $table->condition('uid', $id);
    $result = $table->execute()->fetch();
    if (!$result) {
      return false;
    }

    if ($result->up != $parent_id) {
      return RelationStorage::isUpper($result->up, $parent_id);
    } else {
      return true;
    }
  }

}

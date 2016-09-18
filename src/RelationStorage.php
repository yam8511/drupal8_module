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

  /**
   * Delete an entry from the database.
   *
   * @param array $entry
   *   An array containing at least the person identifier 'uid' element of the
   *   entry to delete.
   *
   * @see db_delete()
   */
  public static function delete($entry) {
    db_delete('zoular_relation')
        ->condition('uid', $entry['uid'])
        ->execute();
  }

  /**
   * Read from the database using a filter array.
   *
   * The standard function to perform reads was db_query(), and for static
   * queries, it still is.
   *
   * db_query() used an SQL query with placeholders and arguments as parameters.
   *
   * @param array $entry
   *   An array containing all the fields used to search the entries in the
   *   table.
   *
   * @return object
   *   An object containing the loaded entries if found.
   *
   * @see db_select()
   * @see db_query()
   * @see http://drupal.org/node/310072
   * @see http://drupal.org/node/310075
   */
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

}

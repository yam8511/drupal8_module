<?php

namespace Drupal\zoular;

use Drupal\zoular\RelationStorage;

/**
 * Class ZoularStorage.
 */
class RateStorage {

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
      $return_value = db_insert('zoular_rate')
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
   * Update an entry in the database.
   *
   * @param array $entry
   *   An array containing all the fields of the item to be updated.
   *
   * @return int
   *   The number of updated rows.
   *
   * @see db_update()
   */
  public static function update($entry) {
    $father = RateStorage::getFatherRate($entry['uid']);
    $exist = RateStorage::getData($entry['uid']);

    if ($entry['bg'] != $father->bg || $entry['sg'] != $father->sg 
      || $entry['bb'] != $father->bb || $entry['sb'] != $father->sb) {
        
        #when entry != father's entry (not exist)
        if (!$exist) {
          return RateStorage::insert($entry);
        }

        #when entry != father's entry (exist)
        $result = db_update('zoular_rate')
          ->fields($entry)
          ->condition('uid', $entry['uid'])
          ->execute();
        return $result;
    }

    #when entry == father's entry
    if ($exist) {
      RateStorage::delete($entry);
    }
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
    db_delete('zoular_rate')
        ->condition('uid', $entry['uid'])
        ->execute();
  }

  /**
   * Get Rate
   */
  public static function getRate($id) {
    if (!$id) {
      return null;
    }

    $result = RateStorage::getData($id);

    if (!$result) {
      $result = RateStorage::getFatherRate($id);
    }

    return $result;
  }

  /**
   * Get Rate
   */
  public static function getFatherRate($id) {
    $father = RelationStorage::father($id);
    return RateStorage::getRate($father);
  }

  /**
   * Get Data where uid=$id 
   */
  public static function getData($id) {
    $select = db_select('zoular_rate', 'example');
    $select->fields('example');
    $select->condition('uid', $id);
    return $select->execute()->fetch();
  }

  /**
   * Load
   */
  public static function load($entry = array()) {
    // Read all fields from the zoular_rate table.
    $select = db_select('zoular_rate', 'example');
    $select->fields('example');

    // Add each field and value as a condition to this query.
    foreach ($entry as $field => $value) {
      $select->condition($field, $value);
    }
    // Return the result in object format.
    return $select->execute()->fetchAll();
  }


  public static function belongsTo($id) {

  }

  public static function hasMany($id) {

  }


}

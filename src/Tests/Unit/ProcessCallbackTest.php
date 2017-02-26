<?php

namespace Drupal\entityreference_autocomplete\Tests\Unit;

use Drupal\entityreference_autocomplete\Tests\UnitTest;

/**
 * {@inheritdoc}
 */
class ProcessCallbackTest extends UnitTest {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'Tests of the "#process" callback',
      'group' => 'Entity Reference Autocomplete',
      'description' => 'Ensure that "#process" from element definition works properly.',
    );
  }

  /**
   * Test construction behavior when invalid entity type specified.
   *
   * @see entityreference_autocomplete_is_element_valid()
   */
  public function testWrongEntityType() {
    foreach (array('#default_value', '#value') as $value_property) {
      $this->valueProperty = $value_property;
      $widget = $this->buildForm('dummy', 1);

      foreach (array('#process', '#value_callback', '#element_validate') as $property) {
        $this->assertFalse(array_key_exists($property, $widget));
      }
    }
  }

  /**
   * Test consistency of built element.
   */
  public function testProcessCallback() {
    // Create list of entities, grouped by content type and bundle.
    $data = $this->createEntities();

    foreach (array(
      array(
        '#default_value' => NULL,
        '#autocomplete_path' => FALSE,
        '#era_bundles' => array(),
        '#era_cardinality' => ENTITYREFERENCE_AUTOCOMPLETE_CARDINALITY_UNLIMITED,
        '#era_entity_type' => NULL,
        '#value' => FALSE,
      ),
      // Non-existent entity type.
      array(
        '#default_value' => NULL,
        '#autocomplete_path' => FALSE,
        '#era_bundles' => array(),
        '#era_cardinality' => 900,
        '#era_entity_type' => 'dummy',
        '#value' => FALSE,
      ),
      array(
        '#default_value' => $data['user']['user'][0]['object'],
        '#autocomplete_path' => 'entityreference_autocomplete/autocomplete/user/*/limit=50',
        '#era_bundles' => array(),
        '#era_cardinality' => 1,
        '#era_entity_type' => 'user',
        '#value' => sprintf(
          '%s (%s)',
          $data['user']['user'][0]['label'],
          $data['user']['user'][0]['id']
        ),
      ),
      array(
        // Use mixed default value: full entity object and entity ID.
        '#default_value' => array($data['node']['page'][0]['object'], $data['node']['article'][0]['id']),
        '#autocomplete_path' => 'entityreference_autocomplete/autocomplete/node/page+article/limit=50',
        '#era_bundles' => array_keys($data['node']),
        '#era_cardinality' => 3,
        '#era_entity_type' => 'node',
        '#value' => sprintf(
          '%s (%s), %s (%s)',
          $data['node']['page'][0]['label'],
          $data['node']['page'][0]['id'],
          $data['node']['article'][0]['label'],
          $data['node']['article'][0]['id']
        ),
      ),
    ) as $specification) {
      $widget = $this->buildForm(
        $specification['#era_entity_type'],
        $specification['#default_value'],
        $specification['#era_cardinality'],
        $specification['#era_bundles']
      );

      foreach ($specification as $property => $value) {
        $this->assertEqual($widget[$property], $value);
      }
    }
  }

}

<?php

namespace Drupal\entityreference_autocomplete\Tests;

/**
 * {@inheritdoc}
 */
class ProcessCallbackTest extends BaseTest {

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
   * Test consistency of built element.
   */
  public function testProcessCallback() {
    $data = array();

    // Create list of entities, grouped by content type and bundle.
    foreach (array(
      'user' => array(
        'user' => array(
          $this->drupalCreateUser(),
        ),
      ),
      'node' => array(
        'page' => array(
          $this->drupalCreateNode(array('type' => 'page')),
        ),
        'article' => array(
          $this->drupalCreateNode(array('type' => 'article')),
        ),
      ),
    ) as $entity_type => $bundles) {
      foreach ($bundles as $bundle => $entities) {
        foreach ($entities as $entity) {
          list($entity_id) = entity_extract_ids($entity_type, $entity);

          $data[$entity_type][$bundle][] = array(
            'id' => $entity_id,
            'label' => entity_label($entity_type, $entity),
            'object' => $entity,
          );
        }
      }
    }

    foreach (array(
      array(
        '#default_value' => NULL,
        '#autocomplete_path' => FALSE,
        '#era_bundles' => array(),
        '#era_cardinality' => -1,
        '#era_entity_type' => NULL,
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

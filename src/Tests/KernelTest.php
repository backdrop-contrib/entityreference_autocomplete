<?php

namespace Drupal\entityreference_autocomplete\Tests;

/**
 * Base fixture for "entityreference_autocomplete" tests.
 */
abstract class KernelTest extends \DrupalWebTestCase {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp(array('entityreference_autocomplete_test'));
  }

  /**
   * Create test entities.
   *
   * @param int $users_count
   *   Number of users to create.
   * @param int $pages_count
   *   Number of nodes of type "page" to create.
   * @param int $articles_count
   *   Number of nodes of type "article" to create.
   *
   * @return array[]
   *   An array of arrays with meta information of entities.
   */
  protected function createEntities($users_count = 1, $pages_count = 1, $articles_count = 1) {
    $data = array();
    $metadata = array();

    for ($i = 0; $i < $users_count; $i++) {
      $data['user']['user'][] = $this->drupalCreateUser();
    }

    for ($i = 0; $i < $pages_count; $i++) {
      $data['node']['page'][] = $this->drupalCreateNode(array('type' => 'page'));
    }

    for ($i = 0; $i < $articles_count; $i++) {
      $data['node']['article'][] = $this->drupalCreateNode(array('type' => 'article'));
    }

    // Create list of entities, grouped by content type and bundle.
    foreach ($data as $entity_type => $bundles) {
      foreach ($bundles as $entities) {
        foreach ($entities as $entity) {
          list($entity_id, $entity_vid, $bundle) = entity_extract_ids($entity_type, $entity);

          $metadata[$entity_type][$bundle][] = array(
            'id' => $entity_id,
            'vid' => $entity_vid,
            'type' => $entity_type,
            'label' => entity_label($entity_type, $entity),
            'bundle' => $bundle,
            'object' => $entity,
          );
        }
      }
    }

    return $metadata;
  }

}

<?php

namespace Drupal\entityreference_autocomplete\Tests;

/**
 * {@inheritdoc}
 */
class ValueCallbackTest extends BaseTest {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'Tests of the "#value_callback" callback',
      'group' => 'Entity Reference Autocomplete',
      'description' => 'Ensure that "#value_callback" from element definition works properly.',
    );
  }

  /**
   * Test acceptable values for element definition.
   */
  public function testAcceptableValues() {
    $entity_type = 'user';

    $entity = $this->drupalCreateUser();
    list($entity_id) = entity_extract_ids($entity_type, $entity);

    $entity2 = $this->drupalCreateUser();
    list($entity_id2) = entity_extract_ids($entity_type, $entity2);

    $structure = array(
      'entity_id' => $entity_id,
      'entity_type' => 'dummy',
    );

    $structure2 = array(
      'entity_id' => $entity_id2,
      'entity_type' => 'dummy',
    );

    // Test single-valued field.
    foreach (array(
      'Try single entity ID' => $entity_id,
      'Try list of entity IDs' => array($entity_id),
      'Try single structure' => $structure,
      'Try an array of structures' => array($structure),
      'Try single entity' => $entity,
      'Try list of entities' => array($entity),
    ) as $test_name => $default_value) {
      $label = sprintf('%s (%s)', entity_label($entity_type, $entity), $entity_id);

      $this->buildForm($entity_type, $default_value);
      $this->assertFieldByName('entityreference', $label, $test_name);
    }

    // Test multivalued field.
    foreach (array(
      'Try list of entity IDs (2 values)' => array($entity_id, $entity_id2),
      'Try an array of structures (2 values)' => array($structure, $structure2),
      'Try list of entities (2 values)' => array($entity, $entity2),
    ) as $test_name => $default_value) {
      $label = sprintf(
        '%s (%s), %s (%s)',
        entity_label($entity_type, $entity),
        $entity_id,
        entity_label($entity_type, $entity2),
        $entity_id2
      );

      $this->buildForm($entity_type, $default_value, 2);
      $this->assertFieldByName('entityreference', $label, $test_name);
    }
  }

  /**
   * Test non-acceptable values for element definition.
   */
  public function testNonAcceptableValues() {
    $entity_type = 'user';
    $entity_id = 129031290;
    $entity = new \stdClass();

    $structure = array(
      'entity_id' => $entity_id,
      'entity_type' => 'dummy',
    );

    $wrong_structure1 = array(
      'entity_1d' => $entity_id,
    );

    $wrong_structure2 = array(
      'entity_id' => array($entity_id),
    );

    $wrong_structure3 = array(
      'entity_id' => array($entity),
    );

    foreach (array(
      'Try single entity ID' => $entity_id,
      'Try list of entity IDs' => array($entity_id),
      'Try single, correct, structure' => $structure,
      'Try an array of correct structures' => array($structure),
      'Try single, wrong, structure #1' => $wrong_structure1,
      'Try an array of wrong structures #1' => array($wrong_structure1),
      'Try single, wrong, structure #2' => $wrong_structure2,
      'Try an array of wrong structures #2' => array($wrong_structure2),
      'Try single, wrong, structure #3' => $wrong_structure3,
      'Try an array of wrong structures #3' => array($wrong_structure3),
      'Try single entity' => $entity,
      'Try list of entities' => array($entity),
    ) as $test_name => $default_value) {
      $this->buildForm($entity_type, $default_value);
      $this->assertFieldByName('entityreference', '', $test_name);
    }
  }

}

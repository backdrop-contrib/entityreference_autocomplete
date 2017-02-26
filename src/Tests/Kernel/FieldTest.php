<?php

namespace Drupal\entityreference_autocomplete\Tests\Kernel;

use Drupal\entityreference_autocomplete\Tests\KernelTest;

/**
 * {@inheritdoc}
 */
class FieldTest extends KernelTest {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'Tests of "entityreference" element',
      'group' => 'Entity Reference Autocomplete',
      'description' => 'Ensure "entityreference" element operates properly.',
    );
  }

  /**
   * Test saving the results of form submission.
   *
   * @see entityreference_autocomplete_test_menu()
   */
  public function testFormSubmit() {
    // Create nodes: one of "page" content type and second - "article".
    $data = $this->createEntities(0, 1, 1);
    $ids = array();

    // Forming a list of entity IDs, to put them into path, to pre-fill
    // the "#default_value" of field.
    foreach ($data['node'] as $entities) {
      foreach ($entities as $entity) {
        $ids[] = $entity['id'];
      }
    }

    // Submit the form.
    $this->drupalPost('entityreference-autocomplete/tests/form/node/' . implode(',', $ids), array(), t('Save configuration'));
    // Results must be stored into variable.
    $results = variable_get('entityreference');

    // Compare number of created entities for test and saved, after form
    // has been submitted.
    if ($this->assertEqual(count($results), count($data['node']))) {
      $uuid_enabled = module_exists('uuid');

      foreach ($results as $i => $result) {
        if ($this->assertFalse(empty($data[$result['entity_type']][$result['entity_bundle']]))) {
          foreach ($data[$result['entity_type']][$result['entity_bundle']] as $entity) {
            $metadata = array(
              'entity_id' => $entity['id'],
              'entity_vid' => $entity['vid'],
              'entity_label' => $entity['label'],
              'entity_type' => $result['entity_type'],
              'entity_bundle' => $result['entity_bundle'],
            );

            if ($uuid_enabled) {
              $uuids = entity_get_uuid_by_id($result['entity_type'], array($entity['id']));
              $metadata['entity_uuid'] = reset($uuids);
            }

            $this->assertEqual($result, $metadata);
          }
        }
      }
    }
  }

  /**
   * Test saved metadata with if UUID module enabled.
   */
  public function testFormSubmitUuid() {
    module_enable(array('uuid'));
    $this->testFormSubmit();
  }

  /**
   * Test validation message when no entities matching field value.
   */
  public function testFormValidateNoMatchingEntities() {
    $this->drupalPost('entityreference-autocomplete/tests/form/node/dummy', array(), t('Save configuration'));
    $this->assertRaw(t('There are no entities matching "%value".', array('%value' => 'dummy')));
  }

  /**
   * Test validation message when multiple entities matching field value.
   */
  public function testFormValidateMultipleEntitiesForReference() {
    // Create two nodes with very similar labels.
    $nodes = array('Page 1', 'Page 2');
    $multiples = array();

    foreach ($nodes as $i => $title) {
      $nodes[$i] = $this->drupalCreateNode(array(
        'title' => $title,
      ));

      list($label) = entityreference_autocomplete_label_for_reference('node', $nodes[$i]);
      $multiples[] = $label;
    }

    // Submit the form with field value which matches multiple results.
    $this->drupalPost('entityreference-autocomplete/tests/form/node/Page', array(), t('Save configuration'));
    $this->assertRaw(t('Multiple entities match this reference: "%multiple". Specify the one you want by appending the ID in parentheses, like "@value (@id)"', array(
      '%multiple' => implode('", "', $multiples),
    )));
  }

}

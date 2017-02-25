<?php

namespace Drupal\entityreference_autocomplete\Tests;

/**
 * Base fixture for "entityreference_autocomplete" tests.
 */
abstract class BaseTest extends \DrupalWebTestCase {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp(array('entityreference_autocomplete_test'));
  }

  /**
   * {@inheritdoc}
   */
  protected function tearDown() {
    parent::tearDown();

    unset(
      $_SESSION['entity_type'],
      $_SESSION['default_value'],
      $_SESSION['cardinality'],
      $_SESSION['bundles']
    );
  }

  /**
   * Build the form with "entityreference" field.
   *
   * @param string $entity_type
   *   Entity type to reference.
   * @param mixed $default_value
   *   Default value for the reference field.
   * @param int $cardinality
   *   Number of allowed references per field.
   * @param string[] $bundles
   *   List of entity bundles for filtering.
   *
   * @see entityreference_autocomplete_test_form()
   */
  protected function buildForm($entity_type, $default_value, $cardinality = 1, array $bundles = array()) {
    $_SESSION['entity_type'] = $entity_type;
    $_SESSION['default_value'] = $default_value;
    $_SESSION['cardinality'] = $cardinality;
    $_SESSION['bundles'] = $bundles;

    $form = drupal_get_form('entityreference_autocomplete_test_form');
    $this->drupalSetContent(drupal_render($form));
  }

}

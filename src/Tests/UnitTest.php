<?php

namespace Drupal\entityreference_autocomplete\Tests;

/**
 * Base fixture for "entityreference_autocomplete" unit tests.
 */
abstract class UnitTest extends KernelTest {

  /**
   * Property, to set element value. One of "#value" or "#default_value".
   *
   * @var string
   */
  protected $valueProperty = '';

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->valueProperty = '#default_value';
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
   * @param array $query_settings
   *   Query settings.
   *
   * @return array
   *   Complete "entityreference" element that was built.
   *
   * @see entityreference_autocomplete_test_programmatic_form()
   */
  protected function buildForm($entity_type, $default_value, $cardinality = 1, array $bundles = array(), array $query_settings = array()) {
    $form = drupal_get_form('entityreference_autocomplete_test_form', $entity_type, $default_value, $cardinality, $bundles, $query_settings, $this->valueProperty);
    $this->drupalSetContent(drupal_render($form));

    return $form['entityreference'];
  }

}

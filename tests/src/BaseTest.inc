<?php

namespace Drupal\Tests\entity_reference_autocomplete;

/**
 * Base fixture for "entityreference_autocomplete" tests.
 */
abstract class BaseTest extends \DrupalWebTestCase {

  /**
   * Description of the test.
   */
  const DESCRIPTION = '';

  /**
   * {@inheritdoc}
   */
  final public static function getInfo() {
    $info = array();
    $info['name'] = static::class;
    $info['group'] = 'Entity';

    if (!empty(static::DESCRIPTION)) {
      $info['description'] = static::DESCRIPTION;
    }

    return $info;
  }

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
  protected function buildForm($entity_type, $default_value, $cardinality = 1, array $bundles = []) {
    $_SESSION['entity_type'] = $entity_type;
    $_SESSION['default_value'] = $default_value;
    $_SESSION['cardinality'] = $cardinality;
    $_SESSION['bundles'] = $bundles;

    $form = drupal_get_form('entityreference_autocomplete_test_form');
    $this->drupalSetContent(drupal_render($form));
  }

}

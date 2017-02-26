<?php

/**
 * @file
 * Autocomplete callback for "entityreference" Form API element.
 */

/**
 * Callback for the "#autocomplete_path" of the"entityreference" element.
 *
 * @param string $entity_type
 *   The type of entity.
 * @param string[] $bundles
 *   List of bundles.
 * @param array $query_settings
 *   Query settings.
 * @param string $string
 *   Search query.
 *
 * @see entityreference_autocomplete_menu()
 * @see entityreference_autocomplete_process_entityreference()
 */
function entityreference_autocomplete_autocomplete_callback($entity_type, array $bundles, array $query_settings, $string) {
  // Get entity metadata, to be used for some checks.
  $entity_info = entity_get_info($entity_type);

  // Entity type is not defined. Bye-bye.
  if (NULL === $entity_info) {
    return;
  }

  // If search string contains a slash "/", menu system will think it's a new
  // argument. In such case, reassemble the whole search string.
  if (func_num_args() > 4) {
    $string = implode('/', array_slice(func_get_args(), 3));
  }

  $matches = array();
  // $context will hold the metadata of entities found, so that modules
  // can alter the results in any way they want.
  $context = array();

  // The user enters a comma-separated list of entity labels. Autocomplete only
  // the last label.
  $labels_typed = entityreference_autocomplete_explode_tags($string);
  $last_label = drupal_strtolower(array_pop($labels_typed));

  $query = new EntityFieldQuery();
  $query->entityCondition('entity_type', $entity_type);
  // Fetch the column to use as label.
  $query->propertyCondition(entityreference_autocomplete_resolve_entity_label_column($entity_type, $entity_info), $last_label, 'CONTAINS');
  $query->addMetaData('era_search_string', $last_label);
  // Add a tag to the query so modules can alter it.
  $query->addTag('era_query');

  // Save some pain to anyone trying to specify a bundle when the entity
  // has no actual key for bundles.
  if (!empty($bundles) && !empty($entity_info['entity keys']['bundle'])) {
    $query->entityCondition('bundle', $bundles);
  }

  // Set property conditions, if any.
  if (!empty($query_settings['property_conditions']) && is_array($query_settings['property_conditions'])) {
    foreach ($query_settings['property_conditions'] as $property_condition) {
      if (is_array($property_condition) && count($property_condition) > 1) {
        call_user_func_array(array($query, 'propertyCondition'), $property_condition);
      }
    }
  }

  // Add the field conditions declared.
  if (!empty($query_settings['field_conditions']) && is_array($query_settings['field_conditions'])) {
    // @code
    // array('COLUMN', 'VALUE', 'OPERATOR', 'DELTA_GROUP', 'LANGUAGE_GROUP')
    // @endcode
    foreach ($query_settings['field_conditions'] as $field_condition) {
      if (is_array($field_condition) && count($field_condition) > 0) {
        call_user_func_array(array($query, 'fieldCondition'), $field_condition);
      }
    }
  }

  // Set the maximum number of results returned.
  if (!empty($query_settings['limit']) && is_numeric($query_settings['limit'])) {
    $query->range(0, $query_settings['limit']);
  }

  // Execute query and log any unexpected errors.
  try {
    $result = $query->execute();
  }
  catch (Exception $e) {
    watchdog_exception('entityreference_autocomplete', $e);
  }

  if (!empty($result[$entity_type])) {
    $get_uuid = function_exists('entity_get_uuid_by_id');
    // Prefix string to add to every result.
    $prefix = '';

    if (!empty($last_label)) {
      $prefix = count($labels_typed) ? implode(', ', $labels_typed) . ', ' : '';
    }

    // Iterate through all entities retrieved and process the data to return
    // it as expected by Drupal javascript.
    foreach (entity_load($entity_type, array_keys($result[$entity_type])) as $entity) {
      if (entityreference_autocomplete_entity_access('view', $entity_type, $entity, $entity_info)) {
        // Get the labels for the key and for the option.
        list($label, $raw_label, $entity_id, $entity_vid, $bundle) = entityreference_autocomplete_label_for_reference(
          $entity_type,
          $entity
        );

        $result_context = array(
          'entity' => $entity,
          'entity_id' => $entity_id,
          'entity_vid' => $entity_vid,
          'entity_type' => $entity_type,
          'entity_uuid' => NULL,
          'entity_bundle' => $bundle,
          'rendered_html' => check_plain($raw_label),
        );

        if ($get_uuid) {
          $uuids = entity_get_uuid_by_id($entity_type, array($entity_id));
          $result_context['entity_uuid'] = empty($uuids) ? NULL : reset($uuids);
        }

        // "$prefix . $key" - is the value that will be set in textfield
        // in browser, whereas "$option" - is HTML that shown to the user
        // *before* he have chosen one of the options.
        $matches[$prefix . $label] = $result_context['rendered_html'];
        $context[$prefix . $label] = $result_context;
      }
    }
  }

  // Let other drupal modules alter the results.
  drupal_alter('entityreference_autocomplete_matches', $matches, $context);
  // Finally, output matches in json, as they're are expected by Drupal's ajax.
  drupal_json_output($matches);
}

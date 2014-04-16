
Written by Salvador Molina <salvador.molinamoreno@codeenigma.com>
                           <drupal.org: https://drupal.org/user/826088>


CONTENTS OF THIS FILE
---------------------

 * INTRODUCTION
 * INSTALLATION
 * USAGE
 * ELEMENT PROPERTIES
 * ROADMAP


INTRODUCTION
------------

This module defines a new form element type, called "entityreference", that
allows developers to add autocomplete fields to forms, so that users can
reference any entity in the same way they would do through an Entity Reference
field.


INSTALLATION
------------

To install the Entity Reference Autocomplete module:

 1. Place its entire folder into the "sites/all/modules/contrib" folder of your
    drupal installation.

 2. In your Drupal site, navigate to "admin/modules", search the "Entity
    Reference Autocomplete" module, and enable it.

 3. Click on "Save configuration".


USAGE
-----

After installing the module:

  1. Create any form you want in the usual way through drupal_get_form().

  2. To define an entityreference field, declare it as any other form element,
     specifying 'entityreference' in the '#type' property of the element. E.g:

        $form['my_entity_reference'] = array(
          '#type' => 'entityreference',
          '#title' => t('My Reference'),
          '#era_entity_type' => 'user',  // Mandatory.
          '#era_bundles' => array(), // Optional (Any bundle by default).
          '#era_cardinality' => 3,       // Optional (1 By default).
        );

  3. When the form is rendered, you should have the autocomplete field ready to
     use.

  4. For a detailed explanation of the meaning of every '#era_{property}'
     property, see the "ELEMENT PROPERTIES" section of this README.


ELEMENT PROPERTIES
------------------

Explanation of the custom properties used in an 'entityreference' form element,
and any Form API standard properties which use might not be clear:

'#era_entity_type':  The Entity Type to be referenced: Like "user", "node",
                     "comment", "file", "taxonomy_term", etc...

'#era_bundles':      Serves to specify that only entities of a given bundle will
                     be returned in the autocomplete field. For nodes, it would
                     be the content type, like "story", "page", etc...

'#era_cardinality':  The maximum number of items (references) that the field
                     will accept. The user will be able to reference more items
                     than this number in the browser, but then the form won't
                     validate on submission, and an error message will appear on
                     the page telling him about this restriction.

                     For unlimited values, use ERA_CARDINALITY_UNLIMITED or -1.

'#era_query_settings':  Serves to specify certain settings that will affect the
                        results returned by the autocomplete callback. This
                        property MUST be an associative array, in which every
                        key is the name of a supported setting. At the moment,
                        the settings supported are:

              - 'limit':  Limits the number of results returned by the callback.
                          This is useful when there are a lot of entities that
                          match the text entered by the users, so that the
                          request doesn't make a big impact when querying the
                          database (it translates to a $query->range(0, limit)).
                          The default value is 50, but it can be set to any
                          integer value. Unset it, or set it to NULL for no
                          limits.

'#default_value':    If references to any entities are provided by default, it
                     should be as Entity IDs. For single values, just pass the
                     ID of the referenced entity. For multiple values, an array
                     of Entity IDs is expected.


ROADMAP
-------

TODO: Try to get entities from the label on validate function, when users don't
use the autocomplete widget and simply enters a value manually.

The following features might be added soon:

 * Filtering by any column of the entity table (instead of just the label).
 * Filtering by the value of any field of the entity.

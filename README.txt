
Written by Salvador Molina <salvador.molinamoreno@codeenigma.com>
                           <drupal.org: https://drupal.org/user/826088>


CONTENTS OF THIS FILE
---------------------

 * INTRODUCTION
 * INSTALLATION
 * USAGE
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

ROADMAP
-------

TODO: Try to get entities from the label on validate function, when users don't
use the autocomplete widget and simply enters a value manually.

The following features might be added soon:

 * Filtering by any column of the entity table (instead of just the label).
 * Filtering by the value of any field of the entity.

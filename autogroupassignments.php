<?php

require_once 'autogroupassignments.civix.php';
require_once 'tools.php';

/**
 * Implementation of hook_civicrm_config
 */
function autogroupassignments_civicrm_config(&$config) {
  _autogroupassignments_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function autogroupassignments_civicrm_xmlMenu(&$files) {
  _autogroupassignments_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function autogroupassignments_civicrm_install() {
  return _autogroupassignments_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function autogroupassignments_civicrm_uninstall() {
  return _autogroupassignments_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function autogroupassignments_civicrm_enable() {
  return _autogroupassignments_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function autogroupassignments_civicrm_disable() {
  return _autogroupassignments_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function autogroupassignments_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _autogroupassignments_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function autogroupassignments_civicrm_managed(&$entities) {
  return _autogroupassignments_civix_civicrm_managed($entities);
}

/**
 * When someone opens the contact create form, it sets the correspondant
 * groups by default.
 * 
 * Assigns a new contact to groups depending on the groups the author
 * belongs to.
 */
function autogroupassignments_civicrm_buildForm($formName, &$form) {
  $tools = new AutoGroupAssignmentsTools();
  
  if ( $formName == 'CRM_Contact_Form_Contact' ) {
    if ( $form->getAction() == CRM_Core_Action::ADD ) {
      $authorId = $tools->author_id();
      $contact_group = CRM_Contact_BAO_GroupContact::getContactGroup($authorId);
      
      foreach( $contact_group as $group ) {
        $default_groups[] = $tools->get_default_group( $group['group_id'] );
      }
      
      $defaults['group'] = $default_groups;
      $form->setDefaults( $defaults );
    }
  }
}

/**
 * When a contact is being created via API, it assigns the new contacts
 * to the correspondant groups.
 * 
 * @param string $op Operation being performed with CiviCRM object.
 * @param string $objectName Object that fired the hook.
 * @param type $objectId
 * @param type $objectRef
 */
function autogroupassignments_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  $tools = new AutoGroupAssignmentsTools();
  
  if( ( $op == 'create' ) && $tools->is_contact( $objectName ) ) {
    if( $tools->is_api_call() || true ) {
      
      $authorId = $tools->author_id();
      $authorGroups = CRM_Contact_BAO_GroupContact::getContactGroup($authorId);
      foreach( $authorGroups as $authorGroup ) {
        
        $authorGroupId = $authorGroup['group_id'];
        if( $tools->get_affects_api( $authorGroupId ) ) {
          
          $defaultGroupId = $tools->get_default_group( $authorGroupId );
          if( !CRM_Contact_BAO_GroupContact::isContactInGroup($objectId, $defaultGroupId ) ) {
            
            $contactIds = array( $objectId );
            CRM_Contact_BAO_GroupContact::addContactsToGroup(
              $contactIds,
              $defaultGroupId );
            
          }
        }
      }
    }
  }
}

/**
 * Validates the default group for new contacts to ensure that the id of the
 * group, already exists.
 */
function autogroupassignments_civicrm_validateForm( $formName, &$fields, &$files, &$form, &$errors ) {
  $tools = new AutoGroupAssignmentsTools();
  
  if( $formName == 'CRM_Group_Form_Edit' ) {
    foreach( $fields as $fieldKey => $fieldValue ) {
      if( CRM_Core_BAO_CustomField::getKeyID( $fieldKey ) == $tools->default_group_field_id() ) {
        if( $fieldValue ) {
          if( !CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Group', $fieldValue, 'id' ) ) {
            $errors[ $fieldKey ] = ts( 'Couldn\'t find the default group for new contacts.' );
          }
        }
      }
    }
    
    return;
  }
}

function autogroupassignments_civicrm_customFieldOptions( $fieldID, &$options, $detailedFormat = false ) {
  $tools = new AutoGroupAssignmentsTools();
  
  if( $fieldID == $tools->default_group_field_id() ) {
    $groups_params = array(
      'is_active' => '1'
    );
    
    $groups = CRM_Contact_BAO_Group::getGroups( $groups_params );
    foreach( $groups as $group ) {
      $detailed_options['group_id_' . $group->id] = array(
        'id' => $group->id,
        'value' => $group->id,
        'label' => $group->title
      );
    }
    
    if (isset($detailed_options) && !$detailedFormat ) {
      foreach ($detailed_options AS $key => $choice) {
        $options[$choice['value']] = $choice['label'];
      }
    } else {
      $options += $detailed_options;
    }
  }
}
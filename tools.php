<?php

/**
 * Encapsulates some tools used all along the module.
 */
class AutoGroupAssignmentsTools {
  /**
   * Returns the Id of the user modifying the contact record. It maybe
   * the user who's logged in, or the user related to the API Key
   * performing the change.
   */
  function author_id() {
    $authorId = $this->get_session_user();

    if (!$authorId) {
      $authorId = $this->get_api_user();
    }

    return $authorId;
  }

  /**
   * Returns the id of the user working with CiviCRM. If there's no
   * session object (public webforms or API calls), returns an
   * empty string.
   * 
   * @return string
   */
  function get_session_user() {
    $session = CRM_Core_Session::singleton();
    return $session->get('userID');
  }
  
  /**
   * Returns the id of the user related with the working API Key.
   * It's useful when working with the API. If CiviCRM isn't being
   * used trought the API, returns an empty string.
   * 
   * @return string
   */
  function get_api_user() {
      $authorId = "";
      $store = NULL;
    
      $api_key = CRM_Utils_Request::retrieve(
        'api_key', 'String', $store, FALSE, NULL, 'REQUEST');

      if ($api_key && strtolower($api_key) != 'null') {
        $authorId = CRM_Core_DAO::getFieldValue(
          'CRM_Contact_DAO_Contact', $api_key, 'id', 'api_key');
      }
      
      return $authorId;
  }
  
  /**
   * Returns true if we are in the context of an API call, or
   * false elsewhere.
   * 
   * @return boolean
   */
  function is_api_call() {
    return ( !$this->get_session_user() && $this->get_api_user() );
  }
  
  /**
   * Returns the Id for the custom field Default_Group_for_New_Contacts.
   */
  function default_group_field_id() {
    return CRM_Core_BAO_CustomField::getCustomFieldID(
      'Default_Group_for_New_Contacts',
      'Automatic_Group_Assignments');
  }

  /**
   * Returns the Id for the boolean option Affects_API_calls.
   */
  function affects_api_field_id() {
    $fieldId = CRM_Core_BAO_CustomField::getCustomFieldID(
      'Affects_API_Calls',
      'Automatic_Group_Assignments');
    
    return $fieldId;
  }
  
  /**
   * Obtains the default group for new contacts assigned
   * to a given Group.
   * 
   * @param string $group_id The group assosiated to the default value
   * that we're looking for.
   * @return string
   */
  function get_default_group( $group_id ) {
    return $this->get_group_custom_value( $group_id, $this->default_group_field_id() );
  }
  
  /**
   * Returns the value of the check "Affects API Calls" for a given group.
   * 
   * @param type $group_id
   * @return type
   */
  function get_affects_api( $group_id ) {
    return $this->get_group_custom_value( $group_id, $this->affects_api_field_id() );
  }
  
  /**
   * Obtains the value of a group custom field, given the group Id.
   * 
   * @param string $group_id The group id of the value that we're looking for.
   * @param string $field_id The id of the custom field.
   * @return string
   */
  function get_group_custom_value( $group_id, $field_id ) {
    $custom_field = array( 'custom_field' => 'custom_' . $field_id );
    
    $get_params = array(
      'entityID' => $group_id,
      'entityTable' => 'Group',
      $custom_field['custom_field'] => 1
    );
    
    $values = CRM_Core_BAO_CustomValueTable::getValues($get_params);
    return $values['custom_' . $field_id];
  }
  
  /**
   * Returns true if the given object name corresponds to a Contact.
   * False elsewhere.
   * 
   * @param string $objectName
   * @return boolean
   */
  function is_contact( $objectName ) {
    return ( $objectName == 'Individual' || $objectName == 'Household' || $objectName == 'Organization' );
  }
}
?>

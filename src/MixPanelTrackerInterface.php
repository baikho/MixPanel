<?php

namespace Drupal\mixpanel;

/**
 * Interface for MixpanelTracker classes.
 */
interface MixPanelTrackerInterface {

  /**
   * Track an event defined by $event associated with metadata defined by $properties
   * @param string $event
   * @param array $properties
   */
  public function track($event, $properties = array());

  /**
   * Set properties on a user record. If the profile does not exist, it creates it with these properties.
   * If it does exist, it sets the properties to these values, overwriting existing values.
   * @param string|int $distinct_id the distinct_id or alias of a user
   * @param array $props associative array of properties to set on the profile
   * @param string|null $ip the ip address of the client (used for geo-location)
   * @param boolean $ignore_time If the $ignore_time property is true, Mixpanel will not automatically update the "Last Seen" property of the profile. Otherwise, Mixpanel will add a "Last Seen" property associated with the current time
   */
  public function setUser($distinct_id, $props, $ip = NULL, $ignore_time = FALSE);

  /**
   * Delete this profile from Mixpanel
   * @param string|int $distinct_id the distinct_id or alias of a user
   * @param string|null $ip the ip address of the client (used for geo-location)
   * @param boolean $ignore_time If the $ignore_time property is true, Mixpanel will not automatically update the "Last Seen" property of the profile. Otherwise, Mixpanel will add a "Last Seen" property associated with the current time
   */
  public function deleteUser($distinct_id, $ip = NULL, $ignore_time = FALSE);

}

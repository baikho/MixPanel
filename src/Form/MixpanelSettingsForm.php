<?php

/**
 * @file
 * Contains \Drupal\mixpanel\Form\MixpanelSettingsForm.
 */

namespace Drupal\mixpanel\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure mixpanel settings for this site.
 */
class MixpanelSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'mixpanel_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'mixpanel.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('mixpanel.settings');

    $form['mixpanel_token'] = array(
      '#title' => $this->t('Mixpanel Token'),
      '#type' => 'textfield',
      '#default_value' => $config->get('mixpanel_token'),
      '#description' => $this->t('The token you got from mixpanel.com for this domain.'),
      '#required' => TRUE,
    );

    $form['mixpanel_user'] = array(
      '#title' => $this->t('Mixpanel User integration'),
      '#type' => 'checkbox',
      '#default_value' => $config->get('mixpanel_user', TRUE),
      '#description' => $this->t('The token for integration with user profile.'),
    );

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save configuration'),
      '#button_type' => 'primary',
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('mixpanel.settings')
      ->set('mixpanel_token', $form_state->getValue('mixpanel_token'))
      ->set('mixpanel_user', $form_state->getValue('mixpanel_user'))
      ->save();

    drupal_set_message($this->t('For the changes to take effect, you need to clear the cache.'), 'warning');
  }

}

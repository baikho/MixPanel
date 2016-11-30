<?php

namespace Drupal\mixpanel;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\user\Form\UserCancelForm;

/**
 * Provides a confirmation form for cancelling user account.
 */
class MixPanelUserCancelForm extends UserCancelForm {

  /**
   * The MixPanel Tracker.
   *
   * @var \Drupal\mixpanel\MixPanelTrackerInterface
   */
  protected $mixPanelTracker;

  /**
   * Constructs a MixPanelUserCancelForm object.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\mixpanel\MixPanelTrackerInterface $mix_panel_tracker
   *   The MixPanel Tracker.
   */
  public function __construct(EntityManagerInterface $entity_manager, MixPanelTrackerInterface $mix_panel_tracker) {
    parent::__construct($entity_manager);
    $this->mixPanelTracker = $mix_panel_tracker;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager'),
      $container->get('mixpanel.tracker')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $account = $this->entity;
    // Delete user  from mixpanel.com
    $this
      ->mixPanelTracker
      ->deleteUser($account->id());
  }

}

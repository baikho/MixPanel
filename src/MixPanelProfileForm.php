<?php

namespace Drupal\mixpanel;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\user\ProfileForm;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form handler for the profile forms.
 */
class MixPanelProfileForm extends ProfileForm {

  /**
   * The MixPanel Tracker.
   *
   * @var \Drupal\mixpanel\MixPanelTrackerInterface
   */
  protected $mixPanelTracker;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityManagerInterface $entity_manager, LanguageManagerInterface $language_manager, QueryFactory $entity_query, MixPanelTrackerInterface $mix_panel_tracker) {
    parent::__construct($entity_manager, $language_manager, $entity_query);
    $this->mixPanelTracker = $mix_panel_tracker;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager'),
      $container->get('language_manager'),
      $container->get('entity.query'),
      $container->get('mixpanel.tracker')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    parent::save($form, $form_state);
    /** @var $account \Drupal\user\Entity\User */
    $account = $this->entity;
    // Send user to mixpanel.com
    $this
      ->mixPanelTracker
      ->setUser($account->id(), [
        '$first_name' => $account->getUsername(),
        '$email' => $account->getEmail(),
      ]);
  }

}

<?php

namespace Drupal\mixpanel;

use Drupal\user\RegisterForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;

/**
 * Form handler for the user register forms.
 */
class MixPanelRegisterForm extends RegisterForm {

  /**
   * The MixPanel Tracker.
   *
   * @var \Drupal\mixpanel\MixPanelTrackerInterface
   */
  protected $mixPanelTracker;

  /**
   * Constructs a new MixPanelRegisterForm object.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Entity\Query\QueryFactory $entity_query
   *   The entity query factory.
   * @param \Drupal\mixpanel\MixPanelTrackerInterface $mix_panel_tracker
   *   The MixPanel Tracker.
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

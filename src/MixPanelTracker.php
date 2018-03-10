<?php

namespace Drupal\mixpanel;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Mixpanel;

class MixPanelTracker implements MixPanelTrackerInterface {

  /**
   * The Mixpanel class instance.
   *
   * @var \Mixpanel
   */
  protected $mixpanel;

  /**
   * The factory for configuration objects.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The current user service.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser = [];

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Constructs a MixPanelTracker object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *  The factory for configuration objects.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *  The current user service.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *  The request stack.
   */
  public function __construct(ConfigFactoryInterface $config_factory, AccountProxyInterface $current_user, RequestStack $request_stack) {
    $this->configFactory = $config_factory;
    $token = $this
      ->configFactory
      ->get('mixpanel.settings')
      ->get('mixpanel_token', '');
    $this->mixpanel = Mixpanel::getInstance($token);
    $this->currentUser = $current_user;
    $this->requestStack = $request_stack;
  }

  /**
   * {@inheritdoc}
   */
  public function track($event, $properties = []) {
    if (!$this->currentUser->isAnonymous()) {
      $this->mixpanel->identify($this->currentUser->id());
    }

    // Default value.
    $properties += array(
      'ip' => $this->requestStack->getCurrentRequest()->getClientIp(),
      '$browser' => $this->getBrowser(),
      '$os' => $this->getOs(),
      '$device' => $this->getDevice(),
      '$referrer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
      '$referring_domain' => isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) : '',
    );
    $this->mixpanel->track($event, $properties);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setUser($distinct_id, $props, $ip = NULL, $ignore_time = FALSE) {
    $this->mixpanel->people->set($distinct_id, $props, $ip, $ignore_time);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function deleteUser($distinct_id, $ip = NULL, $ignore_time = FALSE) {
    $this->mixpanel->people->deleteUser($distinct_id, $ip);
    return $this;
  }

  /**
   * The helper function for get current browser.
   */
  protected function getBrowser() {
    if (!isset($_SERVER['HTTP_USER_AGENT'])) {
      return '';
    }
    $ua = $_SERVER['HTTP_USER_AGENT'];

    // NOTE: This wasn't a direct port of the Mixpanel Javascript code, because it
    // uses the navigator.vendor and window.opera properties, that we don't have
    // access to. Luckily, the 'vendor' comes from the user agent, so can use that
    // with hopefully the same effect!
    if (preg_match('/Opera/', $ua)) {
      if (preg_match('/Mini/', $ua)) {
        return 'Opera Mini';
      }
      return 'Opera';
    }
    elseif (preg_match('/(BlackBerry|PlayBook|BB10)/i', $ua)) {
      return 'BlackBerry';
    }
    elseif (preg_match('/Chrome/', $ua)) {
      return 'Chrome';
    }
    elseif (preg_match('/Apple/', $ua)) {
      if (preg_match('/Mobile/', $ua)) {
        return 'Mobile Safari';
      }
      return 'Safari';
    }
    elseif (preg_match('/Android/', $ua)) {
      return 'Android Mobile';
    }
    elseif (preg_match('/Konqueror/', $ua)) {
      return 'Konqueror';
    }
    elseif (preg_match('/Firefox/', $ua)) {
      return 'Firefox';
    }
    elseif (preg_match('/MSIE/', $ua)) {
      return 'Internet Explorer';
    }
    elseif (preg_match('/Gecko/', $ua)) {
      return 'Mozilla';
    }

    return '';
  }

  /**
   * The helper function for get current OS.
   */
  protected function getOs() {
    if (!isset($_SERVER['HTTP_USER_AGENT'])) {
      return '';
    }
    $ua = $_SERVER['HTTP_USER_AGENT'];

    if (preg_match('/Windows/i', $ua)) {
      if (preg_match('/Phone/', $ua)) {
        return 'Windows Mobile';
      }
      return 'Windows';
    }
    elseif (preg_match('/(iPhone|iPad|iPod)/', $ua)) {
      return 'iOS';
    }
    elseif (preg_match('/Android/', $ua)) {
      return 'Android';
    }
    elseif (preg_match('/(BlackBerry|PlayBook|BB10)/i', $ua)) {
      return 'BlackBerry';
    }
    elseif (preg_match('/Mac/i', $ua)) {
      return 'Mac OS X';
    }
    elseif (preg_match('/Linux/', $ua)) {
      return 'Linux';
    }

    return '';
  }

  /**
   * The helper function for get current device.
   */
  protected function getDevice() {
    if (!isset($_SERVER['HTTP_USER_AGENT'])) {
      return '';
    }
    $ua = $_SERVER['HTTP_USER_AGENT'];

    if (preg_match('/iPhone/', $ua)) {
      return 'iPhone';
    }
    elseif (preg_match('/iPad/', $ua)) {
      return 'iPad';
    }
    elseif (preg_match('/iPod/', $ua)) {
      return 'iPod Touch';
    }
    elseif (preg_match('/(BlackBerry|PlayBook|BB10)/i', $ua)) {
      return 'BlackBerry';
    }
    elseif (preg_match('/Windows Phone/i', $ua)) {
      return 'Windows Phone';
    }
    elseif (preg_match('/Android/', $ua)) {
      return 'Android';
    }

    return '';
  }

}

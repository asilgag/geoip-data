<?php

namespace Drupal\geoip_data\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class GeoipDataSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'geoip_data_settings';
  }

  protected function getEditableConfigNames() {
    return ['geoip_data.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('geoip_data.settings');

    $form['geoip_data_db_path'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Path to MaxMind\'s GeoLite2 Country database (relative to %module_path)', ['%module_path' => drupal_get_path('module', 'geoip_data')]),
      '#required' => TRUE,
      '#description' => t('Get MaxMind\' database from <a href="http://dev.maxmind.com/geoip/geoip2/geolite2/" target="_blank">http://dev.maxmind.com/geoip/geoip2/geolite2/</a>'),
      '#default_value' => $config->get('db_path'),
    );


    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = \Drupal::service('config.factory')->getEditable('geoip_data.settings');
    $config
      ->set('db_path', $form_state->getValue('geoip_data_db_path'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}

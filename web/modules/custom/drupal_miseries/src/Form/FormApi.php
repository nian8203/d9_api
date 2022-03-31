<?php

namespace Drupal\drupal_miseries\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure example settings for this site.
 */
class FormApi extends ConfigFormBase {

  /** 
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'example.settings';

  /** 
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'form_api_admin_settings';
  }

  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /** 
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $form['example_thing'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Clave de api bits'),
      '#default_value' => $config->get('example_thing'),
      '#description' => 'Ingrese una clave menor a 15 digitos',
      '#maxlength' => 20,
      '#required' => true,
    ];  

    // $form['other_things'] = [
    //   '#type' => 'textfield',
    //   '#title' => $this->t('Other things'),
    //   '#default_value' => $config->get('other_things'),
    // ];  

    return parent::buildForm($form, $form_state);
  }

  /** 
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration.
    $this->configFactory->getEditable(static::SETTINGS)
      // Set the submitted configuration setting.
      ->set('example_thing', $form_state->getValue('example_thing'))
      // You can set multiple configurations at once by making
      // multiple calls to set().
      ->set('other_things', $form_state->getValue('other_things'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
<?php


namespace Drupal\inversiones\Plugin\Field\FieldWidget;


use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'fondo_inversion_default' widget.
 *
 * @FieldWidget(
 *   id = "inversiones_fondo_inversion_select",
 *   label = @Translation("Inversiones select"),
 *   field_types = {
 *     "inversiones_fondo_inversion"
 *   }
 * )
 */
class FondoInversionDefaultWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * @var Drupal\inversiones\Services\WebServices
   */
  private $service;

  /**
   * @param \Drupal\inversiones\Services\WebServices $web_services
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, $web_services) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->service = $web_services;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id, $plugin_definition, $configuration['field_definition'], $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('inversiones.services'));
  }

  /**
   * @inheritDoc
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state)
  {
    $options = [];
    foreach($this->service->getInstance()->fondosInversion()['fondosInversion'] as $fondo) {
      $options[$fondo['fondo']] = $fondo['empresa'];
    }
    $element['value'] = array(
      '#title' => $this->t('Fondo inversion'),
      '#type' => 'select',
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : NULL,
      '#options' => $options,
    );
    return $element;
  }
}

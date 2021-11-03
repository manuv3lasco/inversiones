<?php


namespace Drupal\inversiones\Plugin\Field\FieldType;


use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'Fondo Mutos WS' field type.
 *
 * @FieldType(
 *   id = "inversiones_fondo_inversion",
 *   label = @Translation("Webservice Fondos de Inversion"),
 *   description = @Translation("Listado de Fondos de Inversion."),
 *   default_widget = "inversiones_fondo_inversion_select",
 *   default_formatter = "inversiones_fondo_inversion_formatter"
 * )
 */
class FondoInversionItem extends FieldItemBase
{

    /**
     * @inheritDoc
     */
    public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition)
    {
      $properties['value'] = DataDefinition::create('string')
        ->setLabel(t('Listado de Fondos de Inversion.'));

      return $properties;
    }

    /**
     * @inheritDoc
     */
    public static function schema(FieldStorageDefinitionInterface $field_definition)
    {
      return array(
        'columns' => array(
          'value' => array(
            'type' => 'varchar',
            'length' => 255,
            'not null' => FALSE,
          ),
        ),
      );
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty() {
      $value = $this->get('value')->getValue();
      return $value === NULL || $value === '';
    }
}

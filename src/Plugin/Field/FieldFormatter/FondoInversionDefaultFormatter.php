<?php


namespace Drupal\inversiones\Plugin\Field\FieldFormatter;


use Drupal\Console\Bootstrap\Drupal;
use Drupal\Core\Field\FieldItemListInterface;


/**
 * Plugin implementation of the 'snippets_default' formatter.
 *
 * @FieldFormatter(
 *   id = "inversiones_fondo_inversion_formatter",
 *   label = @Translation("Fondo inversion default"),
 *   field_types = {
 *     "inversiones_fondo_inversion"
 *   }
 * )
 */
class FondoInversionDefaultFormatter extends \Drupal\Core\Field\FormatterBase {

  /**
   * @inheritDoc
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $fondo_inversion_id = $items->getValue()[0]["value"];
    $elements = [];
    $source = ['id' => $fondo_inversion_id];
    $elements[] = array(
      '#theme' => 'field__fondo_inversion',
      '#attached' =>
        array(
          'library' =>
            array('inversiones/base','gruposecurity/inversiones')
        ),
      '#source' => $source,
    );

    return $elements;
  }

}

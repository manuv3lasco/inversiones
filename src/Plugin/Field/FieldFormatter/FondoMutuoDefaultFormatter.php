<?php


namespace Drupal\inversiones\Plugin\Field\FieldFormatter;


use Drupal\Console\Bootstrap\Drupal;
use Drupal\Core\Field\FieldItemListInterface;


/**
 * Plugin implementation of the 'snippets_default' formatter.
 *
 * @FieldFormatter(
 *   id = "inversiones_fondo_mutuo_formatter",
 *   label = @Translation("Fondo mutuo default"),
 *   field_types = {
 *     "inversiones_fondo_mutuo"
 *   }
 * )
 */
class FondoMutuoDefaultFormatter extends \Drupal\Core\Field\FormatterBase {

  /**
   * @inheritDoc
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $source = [];
    foreach ($items as $delta => $item) {
      $idFondo = $item->value;
    }
    $source['id_fondo'] = $idFondo;
    $series_fm = \Drupal::service('inversiones.services')->getInstance()->fondosMutuosSeries($idFondo);
    foreach ($series_fm as $serie) {
      $source['series'][] = [
        'codigo' => $serie['CodigoSerie'],
        'nombre' => $serie['NombreSerie'],
      ];
    }
    $elements[] = array(
      '#theme' => 'field__fondo_mutuo',
      '#attached' =>
        array(
          'library' =>
            array('inversiones/base')
        ),
      '#source' => $source,
    );

    return $elements;
  }

}

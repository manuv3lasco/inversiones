<?php


namespace Drupal\inversiones\Plugin\views\style;

/**
 * The style plugin for serialized output formats.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "custom_serializer_anexos",
 *   title = @Translation("Inversiones Custom serializer anexos"),
 *   help = @Translation("Serializes views row data using the Serializer
 *   component."), display_types = {"data"}
 * )
 */
class CustomSerializerAnexos extends \Drupal\rest\Plugin\views\style\Serializer {

  public function render() {

    $rows = [];
    // If the Data Entity row plugin is used, this will be an array of entities
    // which will pass through Serializer to one of the registered Normalizers,
    // which will transform it to arrays/scalars. If the Data field row plugin
    // is used, $rows will not contain objects and will pass directly to the
    // Encoder.
    foreach ($this->view->result as $row_index => $row) {
      $this->view->row_index = $row_index;
      $rows[] = $this->view->rowPlugin->render($row);
    }
    unset($this->view->row_index);

    // Get the content type configured in the display or fallback to the
    // default.
    if ((empty($this->view->live_preview))) {
      $content_type = $this->displayHandler->getContentType();
    }
    else {
      $content_type = !empty($this->options['formats']) ? reset($this->options['formats']) : 'json';
    }

    /*foreach ($rows[0] as $label => $item) {
      if($item instanceof \Drupal\views\Render\ViewsRenderPipelineMarkup) {
        $rows[0][$label] = json_decode($item->__toString(),TRUE);
      }
    }*/

    $new_row = [
      'title' => "Anexos Tributarios",
      'archivos' => $rows,
    ];

    return $this->serializer->serialize($new_row, $content_type, ['views_style_plugin' => $this]);

  }

}

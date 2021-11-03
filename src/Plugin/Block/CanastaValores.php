<?php


namespace Drupal\inversiones\Plugin\Block;

use \Drupal\Core\Block\BlockBase;


/**
 * Provides a Valores Cuota security block.
 *
 * @Block(
 *   id = "canasta_valores_inversiones_block",
 *   admin_label = @Translation("Canasta Valores"),
 * )
 */
class CanastaValores extends BlockBase
{

    /**
     * @inheritDoc
     */
    public function build()
    {
      $content = [];
      $renderable = [
        '#theme' => 'block_canasta_valores_inversiones_security',
        '#content' => $content,

      ];
      return $renderable;
    }
}

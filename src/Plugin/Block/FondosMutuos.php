<?php


namespace Drupal\inversiones\Plugin\Block;

use \Drupal\Core\Block\BlockBase;


/**
 * Provides a fondos mutuos security block.
 *
 * @Block(
 *   id = "fondos_mutuos_security_block",
 *   admin_label = @Translation("Fondos Mutuos Security"),
 * )
 */
class FondosMutuos extends BlockBase
{

    /**
     * @inheritDoc
     */
    public function build()
    {
      $content = [];
      $renderable = [
        '#theme' => 'block_fondos_mutuos_security',
        '#content' => $content,

      ];
      return $renderable;
    }
}

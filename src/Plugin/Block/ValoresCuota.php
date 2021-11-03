<?php


namespace Drupal\inversiones\Plugin\Block;

use \Drupal\Core\Block\BlockBase;


/**
 * Provides a Valores Cuota security block.
 *
 * @Block(
 *   id = "valores_cuota_inversiones_block",
 *   admin_label = @Translation("Valores Cuota"),
 * )
 */
class ValoresCuota extends BlockBase
{

    /**
     * @inheritDoc
     */
    public function build()
    {
      $content = [];
      $renderable = [
        '#theme' => 'block_valores_cuota_inversiones_security',
        '#content' => $content,

      ];
      return $renderable;
    }
}

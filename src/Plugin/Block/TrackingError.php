<?php


namespace Drupal\inversiones\Plugin\Block;

use \Drupal\Core\Block\BlockBase;


/**
 * Provides a fondos mutuos security block.
 *
 * @Block(
 *   id = "tracking_error_inversiones_block",
 *   admin_label = @Translation("Tracking Error"),
 * )
 */
class TrackingError extends BlockBase
{

    /**
     * @inheritDoc
     */
    public function build()
    {
      $content = [];
      $renderable = [
        '#theme' => 'block_tracking_error_inversiones_security',
        '#content' => $content,

      ];
      return $renderable;
    }
}

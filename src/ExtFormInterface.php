<?php
/**
 * @file
 * Contains \Drupal\om_ext_forms\ExtFormInterface.
 */

namespace Drupal\om_ext_forms;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a Ext Form entity.
 *
 * We have this interface so we can join the other interfaces it extends.
 *
 * @ingroup om_ext_forms
 */
interface ExtFormInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}

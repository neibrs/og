<?php

namespace Drupal\og\Plugin\OgContext;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\og\GroupManager;
use Drupal\og\OgContextBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @OgContext(
 *  id = "entity",
 *  label = "Entity",
 *  description = @Translation("Get the group from the current entity, by checking if it is a group or a group content entity.")
 * )
 */
class Entity extends OgContextBase {

  /**
   * The route match service.
   *
   * @var RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The group manager service.
   *
   * @var \Drupal\og\GroupManager
   */
  protected $groupManger;

  /**
   * Constructs a Drupal\Component\Plugin\PluginBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match service.
   * @param \Drupal\og\GroupManager $group_manager
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $route_match, GroupManager $group_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routeMatch = $route_match;
    $this->groupManger = $group_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
      $container->get('og.group.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getGroup() {
    foreach ($this->routeMatch->getParameters() as $parameter) {
      if (!($parameter instanceof ContentEntityBase)) {
        continue;
      }

      if ($this->groupManger->isGroup($parameter->getEntityTypeId(), $parameter->bundle())) {
        return $parameter;
      }
    }
  }
}

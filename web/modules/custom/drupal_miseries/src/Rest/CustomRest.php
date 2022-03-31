<?php

namespace Drupal\drupal_miseries\Rest;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class CustomRest extends ControllerBase {
  protected $entityTypeManager;
  
  /**
   * Construct implementation.
   * @param EntityTypeManagerInterface $entityTypeManager
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }
  
  /**
   * Create implementation.
   * @param ContainerInterface $container
   * @return \Drupal\drupal_miseries\Rest\CustomRest
   */
  public static function create(ContainerInterface $container) {
    return new static (
      $container->get('entity_type.manager')
    );
  }
  
  /**
   * Return 10 last nodes created in a Json.
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function getLatestNodes() {
    $response_array = array();
    
    // Get node storage from entitytype.manager service.
    $node_storage = $this->entityTypeManager->getStorage('node');
    
    // Exe a query that return 10 last nodes created.
    $query = $node_storage->getQuery()
    ->condition('type', 'colaboradores_bits')
    ->condition('status', 1)
    ->sort('changed', 'DESC')
    ->range(0, 10)
    ->execute();

    // $query = $node_storage->getQuery()
    // ->condition('type', 'article')
    // ->condition('status', 1)
    // ->sort('changed', 'DESC')
    // ->range(0, 10)
    // ->execute();



    if (\Drupal::request()->query->has('url') ) {

      
        $url = \Drupal::request()->query->get('url');
  
        if (!empty($url)) {
                      
            $query = \Drupal::entityQuery('node')
              ->condition('field_unique_url', $url);
            
            $nodes = $query->execute();
            
            $node_id = array_values($nodes);
            
            
            if (!empty($node_id)) {

            
                $data = Node::load($node_id[0]);
                return new ModifiedResourceResponse($data);
  
            }
        }
    }
    
    // Check if query not return value
    if (!empty($query)) {
      // Load all returned nodes.
      $nodes = $node_storage->loadMultiple($query);
      foreach ($nodes as $node) {
        // Get node title and add to return array.
        $response_array[] = [
          'name' => $node->get('field_nombre')->value,
          'lastname' => $node->get('field_apellido')->value,
          'age' => $node->get('field_edad')->value,
        ];
      }
    } else {
      $response_array[] = [
        'message' => $this->t('No nodes stored.'),
      ];
    }
    
    // Transform array in a Json response.
    $response = new JsonResponse($response_array);
    
    return $response;
  }
}
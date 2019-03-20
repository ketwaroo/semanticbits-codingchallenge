<?php

namespace Drupal\modifiedpageoftheday\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\node\NodeInterface;

/**
 * Service related to fetching nodes that were modified on the current date.
 *
 * @author Yaasir Ketwaroo
 */
class CurrentDayService {

    protected $entityTypeManager;
    protected $time;

    public function __construct(EntityTypeManagerInterface $entityTypeManager, TimeInterface $time) {
        $this->entityTypeManager = $entityTypeManager;
        $this->time              = $time;
    }

    /**
     * 
     * @param int $limit 
     * @return NodeInterface[] Array of nodes.
     */
    public function fetchCurrentlyModifiedNodes($limit = 5) {

        $nodeStorage = $this->entityTypeManager
                ->getStorage('node');

        $nids = $nodeStorage->getQuery()
                ->condition('changed', $this->getCurrentDayCutoffTimestamp(), '>=')
                ->condition('status', NodeInterface::PUBLISHED)
                ->sort('changed', 'DESC')
                ->range(0, (int)$limit)
                ->execute();

        return $nodeStorage->loadMultiple($nids);
    }

    protected function getCurrentDayCutoffTimestamp() {
        return strtotime('today 00:00:00', \Drupal::time()->getRequestTime());
    }

}

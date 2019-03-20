<?php

namespace Drupal\modifiedpageoftheday\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides an example block.
 *
 * @Block(
 *   id = "modifiedpageoftheday_currentmodfiedblock",
 *   admin_label = @Translation("SemanticBits Currently Modified Block"),
 *   category = @Translation("Code Challenge")
 * )
 */
class CurrentModifiedBlock extends BlockBase {

    /**
     * {@inheritdoc}
     */
    public function build() {

        $config  = $this->getConfiguration();
        $service = $this->getCurrentDayService();

        return [
            '#theme'   => 'block_modifiedpageoftheday',
            '#cache'   => [
                'max-age' => 3, // lower value for demo.
                'context' => [
                    // some nodes may be not be accessible to all users.
                    'user.permissions'
                ],
            ],
            '#posts' => $service->fetchCurrentlyModifiedNodes($config['page_limit'] ?? 5),
           
        ];
    }

    /**
     * {@inheritdocs}
     */
    public function blockForm($form, FormStateInterface $form_state) {

        $form   = $this->parent(__FUNCTION__, $form, $form_state);
        $config = $this->getConfiguration();

        $form['page_limit'] = [
            '#type'          => 'number',
            '#default_value' => $config['page_limit'] ?? 5,
            '#min'           => 1,
            '#max'           => 50,
            '#step'          => 1,
            '#title'         => 'Page Limit',
            '#desctiption'   => 'Max number of currently day posts to show',
        ];

        return $form;
    }

    /**
     * {@inheritdocs}
     */
    public function blockSubmit($form, FormStateInterface $form_state) {

        $this->parent(__FUNCTION__, $form, $form_state);

        $values                            = $form_state->getValues();
        $this->configuration['page_limit'] = $values['page_limit'];
    }

    /**
     * Wrapper for unit testing.
     * 
     * @todo Normally this would be in a trait 
     * @codeCoverageIgnore
     * @param string $method Method name.
     * @param variable-length ...$args
     * @return mixed
     */
    protected function parent($method, ...$args) {
        return parent::$method(...$args);
    }

    /**
     * Gets the main service to fetch the pages of the day.
     * 
     * @return \Drupal\modifiedpageoftheday\Service\CurrentDayService
     */
    protected function getCurrentDayService() {
        return \Drupal::service('modifiedpageoftheday.currentday');
    }

}

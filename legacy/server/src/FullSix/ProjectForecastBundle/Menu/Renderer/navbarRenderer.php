<?php

namespace FullSix\ProjectForecastBundle\Menu\Renderer;

class navbarRenderer extends \Knp\Menu\Renderer\TwigRenderer {
    
    public function render(\Knp\Menu\ItemInterface $item, array $options = array()) {
        return parent::render($item, $options);
    }
}

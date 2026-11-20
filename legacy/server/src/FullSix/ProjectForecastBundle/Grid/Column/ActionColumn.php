<?php

namespace FullSix\ProjectForecastBundle\Grid\Column;

use APY\DataGridBundle\Grid\Column\Column;

class ActionColumn extends Column {

    public function __initialize(array $params)
    {
        parent::__initialize($params);

        $this->setFilterable(false);
        $this->setSortable(false);
    }

    public function getType() {
        return 'action';
    }

}
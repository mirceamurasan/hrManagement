<?php

namespace FullSix\ProjectForecastBundle\Twig;

class FullSixExtension extends \Twig_Extension {

    protected $requestStack;

    function __construct($requestStack) {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return array(
            'formActions' => new \Twig_Function_Method($this, 'formActions', array('is_safe' => array('html')))
        );
    }

    /**
     * Converts a string to time
     *
     * @param string $string
     * @return int
     */
    public function formActions() {
        return
                '<div class="form-actions">
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="'.$this->requestStack->getCurrentRequest()->headers->get('referer') .'" type="button" class="btn">Cancel</a>
</div>
';
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName() {
        return 'fullsix_extension';
    }

}
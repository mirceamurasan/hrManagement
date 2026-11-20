<?php

namespace FullSix\ProjectForecastBundle\Service;


class TranslationUtilsService {

    protected $translator;

    public function __construct($translator) {
        $this->translator = $translator;
    }

    /**
     * Creates an html list of errors based on an error array
     *
     * @param $errorsArray
     *
     * @return string
     */
    public function buildErrorHtml($errorsArray) {
        $html = '';
        foreach ($errorsArray as $error)
        {
            $html .= "<li>";
            $html .= $this->translator->trans($error);
            $html .= "</li>";
        }
        return $html;
    }

    /**
     * Creates an array of translated form errors
     *
     * @param $form
     *
     * @return array of strings
     */
    public function buildErrorArrayForForm($form){
        $html = '';
        foreach ($form->getErrors() as $err) {
            $html .= "<h4> - ";
            $html .= $this->translator->trans($err->getMessage());
            $html .= "</h4>";
        }
        return $html;

    }
}
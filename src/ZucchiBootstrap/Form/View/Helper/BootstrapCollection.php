<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zucchi Limited (http://zucchi.co.uk)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZucchiBootstrap\Form\View\Helper;

use Zend\Form\Element;
use Zend\Form\ElementInterface;
use Zend\Form\Element\Collection as CollectionElement;
use Zend\Form\Element\Button;
use Zend\Form\FieldsetInterface;
use Zend\Form\View\Helper\AbstractHelper;
use ZucchiBootstrap\Form\View\Helper\BootstrapRow;


/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zucchi Limited (http://zucchi.co.uk)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class BootstrapCollection extends AbstractHelper
{
    /**
     * the style of form to generate
     * @var string
     */
    protected $formStyle = 'vertical';

    protected $templates = array(
        'vertical' => '<div%s>%s</div>',
        'inline' => '<div%s>%s</div>',
        'search' => '<div%s>%s</div>',
        'horizontal' => '<div%s>%s</div>',
        'table' => '<table%s>%s</table>',
        'tableHead' => '<thead><tr%s>%s</tr></thead>',
        'tableRow' => '<tr%s>%s</tr>',
    );
    
    /**
     * If set to true, collections are automatically wrapped around a fieldset
     *
     * @var boolean
     */
    protected $shouldWrap = true;

    /**
     * @var BootstrapRow
     */
    protected $rowHelper;

    /**
     * Render a collection by iterating through all fieldsets and elements
     *
     * @param  ElementInterface $element
     * @return string
     */
    public function render(ElementInterface $element, $forceStyle = null)
    {
        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            // Bail early if renderer is not pluggable
            return '';
        }
        $options = $element->getOptions();

        // override formstyle
        if ($forceStyle) {
            $this->setFormStyle($forceStyle);
        } else if (isset($options['bootstrap']['displayAs'])) {
            $this->setFormStyle($options['bootstrap']['displayAs']);
        }
        // do this for scope
        $formStyle = $this->getFormStyle();

        $markup = '';
        $attribs = '';

        switch ($formStyle) {
            case 'table':
                $markup .= $this->getTableHeaderMarkup($element);
                $markup .= $this->getTableRowMarkup($element);
                $attribs .= ' id="' . $element->getName() . '-table" ';
                break;
            default:
                $markup .= $this->getElementMarkup($element, $formStyle);
                break;
        }

        $markup .= $this->getTableButtonsMarkup($element, $formStyle);

        $markup .= $this->getTemplateMarkup($element, $formStyle);

        $attribs .= ' ' . $this->createAttributesString($element->getAttributes()) . ' ';

        $markup = sprintf(
            $this->templates[$formStyle],
            $attribs,
            $markup
        );

        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        // Every collection is wrapped by a fieldset if needed
        if ($this->shouldWrap) {
            $label = $element->getLabel();
            if (!empty($label)) {
                $label = $escapeHtmlHelper($label);
                if (null !== ($translator = $this->getTranslator())) {
                    $label = $translator->translate(
                        $label, $this->getTranslatorTextDomain()
                    );
                }
                $markup = sprintf(
                    '<fieldset><legend>%s</legend>%s</fieldset>',
                    $label,
                    $markup
                );
            }
        }
        return $markup;
    }

    /**
     * Invoke helper as function
     *
     * Proxies to {@link render()}.
     *
     * @param  ElementInterface|null $element
     * @param  boolean $wrap
     * @return string|FormCollection
     */
    public function __invoke(ElementInterface $element = null, $style = 'vertical', $wrap = true)
    {
        if (!$element) {
            return $this;
        }
        
        $this->setFormStyle($style);
        
        $this->setShouldWrap($wrap);

        return $this->render($element);
    }

    /**
     * set the style of bootstrap form
     * 
     * @param string $style
     * @return \Zucchi\Form\View\Helper\BootstrapRow
     */
    public function setFormStyle($style)
    {
        $this->formStyle = $style;
        return $this;
    }
    
    /**
     * get the current form style
     * 
     * @return string$this->rowHelper->setFormStyle($this->formStyle);
     */
    public function getFormStyle()
    {
        return $this->formStyle;
    }
    
    /**
     * If set to true, collections are automatically wrapped around a fieldset
     *
     * @param bool $wrap
     * @return FormCollection
     */
    public function setShouldWrap($wrap)
    {
        $this->shouldWrap = (bool)$wrap;
        return $this;
    }

    /**
     * Get wrapped
     *
     * @return bool
     */
    public function shouldWrap()
    {
        return $this->shouldWrap();
    }

    /**
     * Retrieve the BootstrapRow helper
     *
     * @return FormRow
     */
    protected function getRowHelper()
    {
        if ($this->rowHelper) {
            $this->rowHelper->setFormStyle($this->formStyle);
            return $this->rowHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->rowHelper = $this->view->plugin('bootstrap_row');
        }

        if (!$this->rowHelper instanceof BootstrapRow) {
            $this->rowHelper = new BootstrapRow();
        }
        
        return $this->rowHelper;
    }

    /**
     * Get Markup for template
     *
     * @param \Zend\Form\ElementInterface $element
     * @param string $formStyle
     * @return string
     */
    public function getTemplateMarkup(ElementInterface $element, $formStyle)
    {
        $markup = '';
        $templateMarkup = '';
        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $rowHelper = $this->getRowHelper();

        if ($element instanceof CollectionElement && $element->shouldCreateTemplate()) {
            $elementOrFieldset = $element->getTemplateElement();

            if ($elementOrFieldset instanceof FieldsetInterface) {
                $templateMarkup .= $this->render($elementOrFieldset);
            } elseif ($elementOrFieldset instanceof ElementInterface) {
                $templateMarkup .= $rowHelper($elementOrFieldset, $formStyle);
            }
        }

        // If $templateMarkup is not empty, use it for simplify adding new element in JavaScript
        if (!empty($templateMarkup)) {
            $escapeHtmlAttribHelper = $this->getEscapeHtmlAttrHelper();

            $markup .= sprintf(
                '<span id="%s" data-template="%s"></span>',
                $element->getName() . '-template',
                $escapeHtmlAttribHelper($templateMarkup)
            );
        }

        return $markup;
    }


    /**
     * Get Markup for element
     *
     * @param \Zend\Form\ElementInterface $element
     * @param string $formStyle
     * @return string
     */
    public function getElementMarkup(ElementInterface $element, $formStyle)
    {
        $markup = '';
        $rowHelper = $this->getRowHelper();

        foreach($element->getIterator() as $elementOrFieldset) {
            if ($elementOrFieldset instanceof FieldsetInterface) {
                $markup .= $this->render($elementOrFieldset);
            } elseif ($elementOrFieldset instanceof ElementInterface) {
                $markup .= $rowHelper($elementOrFieldset, $formStyle);
            }
        }
        return $markup;
    }

    /**
     * get markup for table header based on elements
     *
     * @param \Zend\Form\ElementInterface $element
     * @return string
     */
    public function getTableHeaderMarkup(ElementInterface $element)
    {
        $markup = '';
        $rowHelper = $this->getRowHelper();

        $elementOrFieldset = $element->getTemplateElement();

        if ($elementOrFieldset instanceof FieldsetInterface) {
            $markup .= $this->render($elementOrFieldset, 'tableHead');
        } elseif ($elementOrFieldset instanceof ElementInterface) {
            $markup .= $rowHelper($elementOrFieldset, 'tableHead');
        }


        return $markup;
    }

    /**
     * get Markup for table row
     *
     * @param \Zend\Form\ElementInterface $element
     * @return string
     */
    public function getTableRowMarkup(ElementInterface $element)
    {
        $options = $element->getOptions();
        $markup = '';
        $rowHelper = $this->getRowHelper();

        foreach($element->getIterator() as $elementOrFieldset) {
            if ($elementOrFieldset instanceof FieldsetInterface) {
                $markup .= $this->render($elementOrFieldset);
            } elseif ($elementOrFieldset instanceof ElementInterface) {
                $markup .= $rowHelper($elementOrFieldset, 'tableRow');
            }
        }



        return $markup;
    }

    public function getTableButtonsMarkup(ElementInterface $element, $formStyle)
    {
        $markup = '';
        $options = $element->getOptions();

        if (isset($options['bootstrap']['buttons'][$formStyle])) {
            $buttons = $options['bootstrap']['buttons'][$formStyle];
            $markup .= '<td>';
                foreach ($buttons as $name => $attribs) {
                    $b = new Button($name);
                    $b->setAttributes($attribs);
                    $markup .= $this->getView()->formButton($b, $name);
                }
            $markup .= '</td>';
        }

        return $markup;
    }
}

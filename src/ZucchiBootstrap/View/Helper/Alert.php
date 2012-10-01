<?php
namespace ZucchiBootstrap\View\Helper;

use Zend\View\Helper\AbstractHtmlElement;

class Alert extends AbstractHtmlElement
{
    public function __invoke($message, $status = 'block', $title = false, $dismissable = true)
    {
        $html = '<div class="alert alert-' . $status . '">';
        
        if ($dismissable) {
            $html .= '<button type="button" class="close" data-dismiss="alert">×</button>';
        }
        
        if ($title) {
            $html .= '<h4>' . $title . '</h4>';
        }
        
        if (is_string($message)) {
            $html .= '<p>' . $message . '</p>';
        } else if (is_array($message)) {
            $html .= $this->getView()->htmlList($message);
        }
        
        $html .= '</div>';
        
        return $html;
    }
}
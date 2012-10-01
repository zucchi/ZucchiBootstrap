<?php
namespace ZucchiBootstrap\View\Helper;

use Zend\View\Helper\AbstractHtmlElement;

class AlertList extends AbstractHtmlElement
{
    public function __invoke($messages)
    {
        $html = '';
        foreach ($messages as $key => $message) {
            $title = false;
            $status = 'block';
            $dismissable = true;
            if (is_array($message)) {
                if (isset($message['title'])) {
                    $title = $message['title'];
                    unset($message['title']);
                }
                if (isset($message['status'])) {
                    $status = $message['status'];
                    unset($message['status']);
                }
                if (isset($message['dismissable'])) {
                    $dismissable = $message['dismissable'];
                    unset($message['dismissable']);
                }
                if (isset($message['message'])) {
                    $message = $message['message'];
                }
            }
            $html .= $this->getView()->bootstrapAlert($message, $status, $title, $dismissable);
        }
        
        return $html;
    }
}
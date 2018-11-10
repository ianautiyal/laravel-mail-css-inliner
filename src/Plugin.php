<?php

namespace Nautiyal\MailCssInliner;

use Pelago\Emogrifier;
use Swift_Events_SendEvent;
use Swift_Events_SendListener;

class Plugin implements Swift_Events_SendListener
{
    /**
    * @var array
    */
    protected $options;

    /**
    * @param array $options options defined in the configuration file.
    */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
    * @param Swift_Events_SendEvent $evt
    */
    public function beforeSendPerformed(Swift_Events_SendEvent $evt)
    {
        $message            = $evt->getMessage();
        $messageBody        = $message->getBody();
        $messageContentType = $message->getContentType();

        if (
            ( $messageContentType === 'text/html' ) ||
            ( $messageContentType === 'multipart/alternative' && $messageBody ) ||
            ( $messageContentType === 'multipart/mixed' && $messageBody )
        ) {
            $message->setBody( $this->renderContents( $messageBody ) );
        }

        foreach ($message->getChildren() as $part) {
            if ( strpos( $part->getContentType(), 'text/html' ) === 0 ) {
                $part->setBody( $this->renderContents( $part->getBody() ) );
            }
        }
    }

    protected function renderContents($html, $css = '')
    {
        $emogrifier = new Emogrifier();
        $emogrifier->setHtml($html);
        $emogrifier->setCss($css);
        return $emogrifier->emogrify(true);
    }

    /**
    * Do nothing.
    * @param Swift_Events_SendEvent $evt
    */
    public function sendPerformed(Swift_Events_SendEvent $evt)
    {
        // Do Nothing
    }
}

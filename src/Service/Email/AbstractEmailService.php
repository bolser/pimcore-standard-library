<?php

/**
 * AbstractEmailService
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md
 * file distributed with this source code.
 *
 * @copyright  Copyright (c) 2001-2017 Bolser Digital Agency (https://www.bolser.co.uk)
 * @license    GNU General Public License version 3 (GPLv3)
 */

namespace Bolser\Pimcore\Service\Email;

use Pimcore\Logger;
use Pimcore\Mail;
use SplFileInfo;
use Zend_Mime;
use Zend_Mime_Part;
use Zend_View;

/**
 * Class AbstractEmailService
 *
 * @package Bolser\Pimcore\Service\Email
 */
abstract class AbstractEmailService
{
    /**
     * @var Mail $mail
     */
    protected $mail;

    /**
     * @var Zend_View $view
     */
    protected $view;

    /**
     * @var string $renderView
     */
    protected $renderView;

    /**
     * @var string $sendTo
     */
    protected $sendTo;

    /**
     * @var string $replyTo
     */
    protected $replyTo;

    /**
     * @var string $subject
     */
    protected $subject;

    /**
     * @var array $carbonCopy
     */
    protected $carbonCopy;

    /**
     * AbstractEmailService constructor.
     *
     * @param Mail        $mail
     * @param ViewFactory $viewFactory
     * @param string      $renderView
     * @param string      $sendTo
     * @param string      $replyTo
     * @param string      $subject
     * @param array       $carbonCopy
     */
    public function __construct(
        Mail $mail,
        ViewFactory $viewFactory,
        string $renderView,
        string $sendTo,
        string $replyTo,
        string $subject,
        array $carbonCopy = []
    ) {
        $this->mail = $mail;
        $this->view = $viewFactory->getView();
        $this->subject = $subject;
        $this->sendTo = $sendTo;
        $this->replyTo = $replyTo;
        $this->subject = $subject;
        $this->carbonCopy = $carbonCopy;
    }

    /**
     * Send an email with HTML as the body
     *
     * @return Mail
     */
    protected function sendHtml(): Mail
    {
        $this->init();

        return $this->mail->send();
    }

    /**
     * Send and email with an attachment and HTML body
     *
     * @param SplFileInfo|null $attachedFile
     *
     * @return Mail
     */
    protected function sendHtmlWithAttachment(SplFileInfo $attachedFile = null): Mail
    {
        if (!file_exists($attachedFile->getRealPath()) || is_null($attachedFile)) {
            Logger::error('Attachment file does not exist. Sending mail without an attachment');

            return $this->sendHtml();
        }

        $content = file_get_contents($attachedFile->getRealPath());

        $attachment = new Zend_Mime_Part($content);
        $attachment->type = mime_content_type($attachedFile->getRealPath());
        $attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
        $attachment->encoding = Zend_Mime::ENCODING_BASE64;
        $attachment->filename = $attachedFile->getFilename();
        $this->mail->addAttachment($attachment);

        return $this->sendHtml();
    }

    /**
     * Initialise the email settings by adding the recipient, sender and body to the mail object
     *
     * @return mixed
     */
    abstract protected function init();
}

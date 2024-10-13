<?php

declare(strict_types=1);

namespace Lemming\AllMailsToMe\EventListener;

use Lemming\AllMailsToMe\Backend\ToolbarItems\AllMailsToMeToolbarItem;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use TYPO3\CMS\Backend\FrontendBackendUserAuthentication;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Mail\Event\BeforeMailerSentMessageEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class OverwriteEnvelopeEventListener
{
    public function __invoke(BeforeMailerSentMessageEvent $event): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $message = $event->getMessage();
        if (!$message instanceof Email) {
            throw new \Exception('Can not handle message class' . $message::class, 1728680813);
        }

        $envelope = Envelope::create($message);
        $originalRecipients = $this->getOriginalRecipientAddresses($envelope);
        $forgedRecipient = $this->getForgedRecipientAddress();
        $envelope->setRecipients([$forgedRecipient]);
        $event->setEnvelope($envelope);
        $this->overwriteSubject($message, $originalRecipients);
    }

    protected function isEnabled(): bool
    {
        return $this->getBackendUser()?->getSessionData(AllMailsToMeToolbarItem::SESSION_DATA_IDENTIFIER) ?? false;
    }

    protected function getForgedRecipientAddress(): Address
    {
        $backendUser = $this->getBackendUser();
        $forgedRecipient = new Address(
            $backendUser->user['email']
        );
        return $forgedRecipient;
    }

    protected function getOriginalRecipientAddresses(Envelope $envelope): array
    {
        $recipientAddresses = [];
        foreach ($envelope->getRecipients() as $recipient) {
            $recipientAddresses[] = $recipient->getAddress();
        }
        return $recipientAddresses;
    }

    protected function overwriteSubject(Email $message, array $originalRecipients): void
    {
        $languageService = GeneralUtility::makeInstance(LanguageServiceFactory::class)
            ->createFromUserPreferences($this->getBackendUser());
        $label = $languageService->sL('LLL:EXT:allmailstome/Resources/Private/Language/locallang.xlf:subjectSuffix');
        $subjectSuffix = vsprintf($label, [implode(', ', $originalRecipients)]);
        $headers = $message->getHeaders();
        $originalSubject = $headers->get('subject')->getBody();
        $headers->remove('subject');
        $headers->addHeader('Subject', implode(' | ', [$originalSubject, $subjectSuffix]));
        $message->setHeaders($headers);
    }

    protected function getBackendUser(): ?FrontendBackendUserAuthentication
    {
        if (($GLOBALS['BE_USER'] ?? null) instanceof FrontendBackendUserAuthentication) {
            return $GLOBALS['BE_USER'];
        }
        return null;
    }
}

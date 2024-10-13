<?php

declare(strict_types=1);

namespace Lemming\AllMailsToMe\Controller;

use Lemming\AllMailsToMe\Backend\ToolbarItems\AllMailsToMeToolbarItem;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[AsController]
class AllMailsToMeController
{
    protected LanguageService $languageService;

    public function __construct(
        protected AllMailsToMeToolbarItem $toolbarItem,
    ) {
        $this->languageService = GeneralUtility::makeInstance(LanguageServiceFactory::class)
            ->createFromUserPreferences($GLOBALS['BE_USER']);
    }

    public function enable(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->toolbarItem->checkAccess()) {
            return new HtmlResponse('Method not allowed', 405);
        }

        if (empty($this->getBackendUser()->user['email'])) {
            return new HtmlResponse($this->languageService->sL('LLL:EXT:allmailstome/Resources/Private/Language/locallang_be.xlf:notification.error.missingMailAddress'), 412);
        }

        $this->getBackendUser()->setAndSaveSessionData(AllMailsToMeToolbarItem::SESSION_DATA_IDENTIFIER, true);

        $label = $this->languageService->sL('LLL:EXT:allmailstome/Resources/Private/Language/locallang_be.xlf:notification.success');
        $message = vsprintf($label, [$this->getBackendUser()->user['email']]);
        return new HtmlResponse($message);
    }

    public function disable(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->toolbarItem->checkAccess()) {
            return new HtmlResponse('Method not allowed', 405);
        }

        $this->getBackendUser()->setAndSaveSessionData(AllMailsToMeToolbarItem::SESSION_DATA_IDENTIFIER, false);
        return new HtmlResponse('');
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}

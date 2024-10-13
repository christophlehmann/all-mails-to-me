<?php

declare(strict_types=1);

namespace Lemming\AllMailsToMe\Backend\ToolbarItems;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Backend\Toolbar\RequestAwareToolbarItemInterface;
use TYPO3\CMS\Backend\Toolbar\ToolbarItemInterface;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

#[Autoconfigure(public: true)]
class AllMailsToMeToolbarItem implements ToolbarItemInterface, RequestAwareToolbarItemInterface
{
    public const SESSION_DATA_IDENTIFIER = 'allMailsToMeEnabled';

    private ServerRequestInterface $request;

    public function __construct(
        private readonly BackendViewFactory $backendViewFactory,
    ) {}

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    /**
     * Checks whether the user has access to this toolbar item.
     */
    public function checkAccess(): bool
    {
        return $this->getBackendUser()->isAdmin() || (bool)($this->getBackendUser()->getTSConfig()['tx_allmailstome.']['enabled'] ?? false);
    }

    public function getItem(): string
    {
        $view = $this->backendViewFactory->create($this->request, ['christophlehmann/all-mails-to-me']);
        $isEnabled = $this->getBackendUser()->getSessionData(self::SESSION_DATA_IDENTIFIER) ?? false;
        $view->assignMultiple([
            'iconState' => $isEnabled ? 'default' : 'disabled',
            'isEnabled' => $isEnabled,
        ]);
        return $view->render('ToolbarItem');
    }

    /**
     * This item has a drop-down.
     */
    public function hasDropDown(): bool
    {
        return false;
    }

    /**
     * Render drop-down.
     */
    public function getDropDown(): string
    {
        return '';
    }

    /**
     * No additional attributes
     */
    public function getAdditionalAttributes(): array
    {
        return [];
    }

    /**
     * Position relative to others
     */
    public function getIndex(): int
    {
        return 1;
    }

    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}

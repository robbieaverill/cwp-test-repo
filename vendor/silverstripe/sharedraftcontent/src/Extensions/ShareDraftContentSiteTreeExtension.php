<?php

namespace SilverStripe\ShareDraftContent\Extensions;

use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\RandomGenerator;
use SilverStripe\ShareDraftContent\Models\ShareToken;

/**
 * @mixin SiteTree
 */
class ShareDraftContentSiteTreeExtension extends DataExtension
{
    /**
     * The number of days a shared link should be valid for, before expiring.
     *
     * @config
     *
     * @var int
     */
    private static $valid_for_days = 30;

    /**
     * @var array
     */
    private static $db = array(
        'ShareTokenSalt' => 'Varchar(16)',
    );

    /**
     * @var array
     */
    private static $has_many = array(
        'ShareTokens' => ShareToken::class,
    );

    /**
     * @var array
     */
    private static $allowed_actions = array(
        'MakeShareDraftLink'
    );

    /**
     * @return string
     */
    public function ShareTokenLink()
    {
        $shareToken = $this->getNewShareToken();

        return Controller::join_links(
            Director::absoluteBaseURL(),
            'preview',
            $this->generateKey($shareToken->Token),
            $shareToken->Token
        );
    }

    /**
     * @return ShareToken
     */
    protected function getNewShareToken()
    {
        if (!$this->owner->ShareTokenSalt) {
            $this->owner->ShareTokenSalt = $this->getNewToken();
            $this->owner->write();
        }

        $found = null;
        $token = null;
        $tries = 1;
        $limit = 5;

        while (!$found && ($tries++ < $limit)) {
            $token = $this->getNewToken();

            $found = ShareToken::get()->filter(array(
                "Token" => $token,
                "PageID" => $this->owner->ID,
            ))->first();
        }

        $config = Config::forClass(__CLASS__);

        $validForDays = $config->valid_for_days;

        $token = ShareToken::create(array(
            "Token" => $token,
            "ValidForDays" => $validForDays,
            "PageID" => $this->owner->ID,
        ));

        $token->write();

        return $token;
    }

    /**
     * @return string
     */
    protected function getNewToken()
    {
        $generator = new RandomGenerator();

        return substr($generator->randomToken('sha256'), 0, 16);
    }

    /**
     * @param string $salt
     *
     * @return string
     */
    public function generateKey($salt)
    {
        return hash_pbkdf2('sha256', $salt, $this->owner->SharedTokenSalt, 1000, 16);
    }

    /**
     * @return string
     */
    public function getShareDraftLinkAction()
    {
        return $this->owner->Link('MakeShareDraftLink');
    }
}

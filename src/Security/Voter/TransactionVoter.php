<?php
/**
 * Transaction voter.
 */

namespace App\Security\Voter;

use App\Entity\Enum\UserRole;
use App\Entity\Transaction;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class TransactionVoter.
 */
class TransactionVoter extends Voter
{
    /**
     * Edit permission.
     *
     * @const string
     */
    public const EDIT = 'EDIT';

    /**
     * View permission.
     *
     * @const string
     */
    public const VIEW = 'VIEW';

    /**
     * Delete permission.
     *
     * @const string
     */
    public const DELETE = 'DELETE';

    /**
     * Security helper.
     */
    private Security $security;

    /**
     * OrderVoter constructor.
     *
     * @param Security $security Security helper
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool Result
     */
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Transaction;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string         $attribute Permission name
     * @param mixed          $subject   Object
     * @param TokenInterface $token     Security token
     *
     * @return bool Vote result
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::EDIT => $this->canEdit($subject, $user),
            self::VIEW => $this->canView($subject, $user),
            self::DELETE => $this->canDelete($subject, $user),
            default => false,
        };
    }

    /**
     * Checks if user can edit transaction.
     *
     * @param Transaction $transaction Transaction entity
     * @param User        $user        User
     *
     * @return bool Result
     */
    private function canEdit(Transaction $transaction, User $user): bool
    {
        return $transaction->getAuthor() === $user or
            (in_array(UserRole::ROLE_ADMIN->value, $user->getRoles()));
    }

    /**
     * Checks if user can view transaction.
     *
     * @param Transaction $transaction Transaction entity
     * @param User        $user        User
     *
     * @return bool Result
     */
    private function canView(Transaction $transaction, User $user): bool
    {
        return $transaction->getAuthor() === $user or
            (in_array(UserRole::ROLE_ADMIN->value, $user->getRoles()));
    }

    /**
     * Checks if user can delete transaction.
     *
     * @param Transaction $transaction Transaction entity
     * @param User        $user        User
     *
     * @return bool Result
     */
    private function canDelete(Transaction $transaction, User $user): bool
    {
        return $transaction->getAuthor() === $user or
            (in_array(UserRole::ROLE_ADMIN->value, $user->getRoles()));
    }
}

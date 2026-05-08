<?php

namespace App\ProjectManagement\Infrastructure\Project\Security\Voter;

use App\ProjectManagement\Domain\Project\Project;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class ProjectVoter extends Voter
{
    public const MANAGE_USER = 'PROJECT_MANAGE_USERS';
    public const EDIT = 'PROJECT_EDIT';


    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute,  [self::MANAGE_USER, self::EDIT])
               && $subject instanceof Project;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        /** @var Project $project */
        $project = $subject;

        if (in_array('ROLE_ADMIN', $user->getRoles())) return true;

        foreach ($project->getProjectUsers() as $assignment) {
            if ($assignment->getAppUser() === $user &&
                $assignment->getProjectRole()->getName() === 'PROJECT_MANAGER') {
                return true;
            }
        }

        return false;
    }
}

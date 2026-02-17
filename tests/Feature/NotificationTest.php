<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\UserOnboardingNotification;
use App\Notifications\GroupInvitationNotification;
use App\Models\Group;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    /**
     * Test that user onboarding notification is sent correctly.
     *
     * @return void
     */
    public function test_user_onboarding_notification_is_sent()
    {
        Notification::fake();

        $user = User::factory()->create();

        $user->notify(new UserOnboardingNotification($user));

        Notification::assertSentTo(
            $user,
            UserOnboardingNotification::class
        );
    }

    /**
     * Test that user onboarding notification includes CC.
     *
     * @return void
     */
    public function test_user_onboarding_notification_with_cc()
    {
        Notification::fake();

        $user = User::factory()->create();
        $cc = 'admin@example.com';

        $user->notify(new UserOnboardingNotification($user, $cc));

        Notification::assertSentTo(
            $user,
            UserOnboardingNotification::class
        );
    }

    /**
     * Test that user onboarding notification includes BCC.
     *
     * @return void
     */
    public function test_user_onboarding_notification_with_bcc()
    {
        Notification::fake();

        $user = User::factory()->create();
        $bcc = 'archive@example.com';

        $user->notify(new UserOnboardingNotification($user, null, $bcc));

        Notification::assertSentTo(
            $user,
            UserOnboardingNotification::class
        );
    }

    /**
     * Test that group invitation notification is sent correctly.
     *
     * @return void
     */
    public function test_group_invitation_notification_is_sent()
    {
        Notification::fake();

        $group = Group::factory()->create();
        $user = User::factory()->create();
        $password = 'temporary_password_123';

        $user->notify(new GroupInvitationNotification($group, $user, $password));

        Notification::assertSentTo(
            $user,
            GroupInvitationNotification::class
        );
    }

    /**
     * Test that group invitation notification includes CC and BCC.
     *
     * @return void
     */
    public function test_group_invitation_notification_with_cc_and_bcc()
    {
        Notification::fake();

        $group = Group::factory()->create();
        $user = User::factory()->create();
        $password = 'temporary_password_123';
        $cc = 'admin@example.com';
        $bcc = 'archive@example.com';

        $user->notify(new GroupInvitationNotification($group, $user, $password, $cc, $bcc));

        Notification::assertSentTo(
            $user,
            GroupInvitationNotification::class
        );
    }
}

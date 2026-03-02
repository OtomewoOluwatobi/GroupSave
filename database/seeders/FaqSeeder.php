<?php

namespace Database\Seeders;

use App\Models\FaqCategory;
use App\Models\FaqArticle;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'slug' => 'account',
                'label' => 'Account & Security',
                'icon' => '🔐',
                'description' => 'Password, 2FA, login issues',
                'color' => '#b77ef8',
                'sort_order' => 1,
                'articles' => [
                    ['q' => 'How do I reset my password?', 'a' => "Tap 'Forgot password?' on the sign-in screen. Enter your registered email and we'll send a 6-digit reset code. Enter the code and choose a new password. The code expires in 24 hours."],
                    ['q' => 'How do I verify my email?', 'a' => "After signing up, check your inbox for a verification email. Click the link or enter the 6-digit code. If not received, tap 'Resend code' on the verification screen."],
                    ['q' => 'How do I enable Two-Factor Authentication?', 'a' => "Go to Profile → Settings → Security → Two-Factor Auth. Link your mobile number or authenticator app. 2FA is strongly recommended for protecting your savings."],
                    ['q' => 'I see a suspicious login — what do I do?', 'a' => "Immediately change your password via Profile → Security. Then contact support@groupsave.app flagged 'Urgent – Security'. We'll investigate and freeze the account if needed."],
                    ['q' => 'How do I change my email or phone number?', 'a' => "Go to Profile → Edit Profile. Update your email or mobile. A verification code will confirm the change before it takes effect."],
                    ['q' => 'How do I appeal an account suspension?', 'a' => "Email support@groupsave.app with subject 'Account Suspension Appeal'. Include your registered email. We review appeals within 24 hours."],
                ],
            ],
            [
                'slug' => 'groups',
                'label' => 'Savings Groups',
                'icon' => '👥',
                'description' => 'Creating, managing and leaving groups',
                'color' => '#7c8cff',
                'sort_order' => 2,
                'articles' => [
                    ['q' => 'How do I create a savings group?', 'a' => "Tap '+' on the Groups screen. Enter the group name, target amount, number of members, start date, and monthly payment day. Invite members by email — they'll receive an invitation."],
                    ['q' => 'How do I invite members?', 'a' => "When creating a group or from the Members tab, enter members' email addresses. They receive an invitation and once accepted, their status changes from Pending to Active."],
                    ['q' => 'What happens if a member misses a payment?', 'a' => "The group admin is notified. The member's status is flagged. Late contributions are still accepted — the system logs all payments with timestamps."],
                    ['q' => 'Can I remove a member from my group?', 'a' => "Yes, if you're the group admin. Group → Members → tap the member → Remove. They'll be notified by email. Their payout position is forfeited."],
                    ['q' => 'What happens if I leave a group mid-cycle?', 'a' => "Contact your admin first. If you've received a payout, leaving mid-cycle affects other members. Open a support ticket if you need mediation."],
                ],
            ],
            [
                'slug' => 'payments',
                'label' => 'Payments & Contributions',
                'icon' => '💳',
                'description' => 'Making payments, failed transactions, disputes',
                'color' => '#34d5a4',
                'sort_order' => 3,
                'articles' => [
                    ['q' => 'How do I make my monthly contribution?', 'a' => "Go to your group → Tap 'Pay Now'. Select your payment method and confirm. You'll receive a confirmation email and group progress updates immediately."],
                    ['q' => 'My payment failed — what do I do?', 'a' => "Check your bank details and ensure sufficient funds. Try again after a few minutes. If it continues, contact support@groupsave.app with your transaction reference number."],
                    ['q' => 'I was charged twice — how do I get a refund?', 'a' => "Email support@groupsave.app with subject 'Duplicate Payment'. Attach your bank statement. Our team investigates within 48 hours and processes refunds within 3–5 business days."],
                    ['q' => 'Can I pay early?', 'a' => "Yes! Early payments are accepted any time. Your contribution is reflected immediately in the group's progress dashboard with the correct timestamp."],
                    ['q' => 'How long do payment confirmations take?', 'a' => "Most payments confirm instantly. In rare cases, allow up to 2 hours. If not confirmed after 24 hours, contact support with your payment reference."],
                ],
            ],
            [
                'slug' => 'payouts',
                'label' => 'Withdrawals & Payouts',
                'icon' => '💰',
                'description' => 'Payout schedules, delays, early withdrawal',
                'color' => '#ffb347',
                'sort_order' => 4,
                'articles' => [
                    ['q' => 'How is the payout schedule determined?', 'a' => "Payout positions are assigned when the group is created, typically by agreement between members. Each member receives the full pot once during the savings cycle."],
                    ['q' => 'When will I receive my payout?', 'a' => "Payouts are processed on the group's payment day. You'll be notified 3 days before. Funds typically arrive within 1–2 business days after processing."],
                    ['q' => 'Can I request an early payout?', 'a' => "Early payout requests require admin approval. Submit via group page → Request Early Payout. The admin reviews within 48 hours."],
                    ['q' => 'My payout is delayed — what should I do?', 'a' => "Allow up to 3 business days from your payout date. If still not received, open a ticket flagged 'High – Payment Delay' with your group ID and payout date."],
                    ['q' => 'What if the group admin fails to pay out?', 'a' => "Contact support immediately — this is a high-priority dispute. Our compliance team investigates payout failures within 24 hours and can intervene on your behalf."],
                ],
            ],
            [
                'slug' => 'notifications',
                'label' => 'Notifications',
                'icon' => '🔔',
                'description' => 'Alerts, reminders & notification settings',
                'color' => '#b77ef8',
                'sort_order' => 5,
                'articles' => [
                    ['q' => 'How do I manage my notifications?', 'a' => "Go to Profile → Settings → Notifications. Toggle push, email, and SMS individually. Control payment reminders, group updates, and invitation alerts separately."],
                    ['q' => "I'm not receiving email notifications", 'a' => "Check your spam/junk folder. Add support@groupsave.app to your contacts. Ensure your email is verified in Profile → Settings."],
                    ['q' => 'Can I set custom payment reminders?', 'a' => "Yes. In Settings → Notifications → Payment Reminders, choose 1, 3, or 7 days before your payment is due. Growth plan users get smart adaptive reminders."],
                ],
            ],
            [
                'slug' => 'billing',
                'label' => 'Subscription & Billing',
                'icon' => '📊',
                'description' => 'Plans, upgrades, cancellations & refunds',
                'color' => '#f4c342',
                'sort_order' => 6,
                'articles' => [
                    ['q' => 'What plans does GroupSave offer?', 'a' => "Starter (Free): 1 group, up to 5 members, basic tracking. Growth (£4.99/month): Unlimited groups, 20 members, smart reminders, advanced analytics, priority support."],
                    ['q' => 'How do I upgrade to Growth?', 'a' => "Go to Profile → Settings → Subscription → Upgrade. Enter payment details. Your plan activates immediately, billed monthly on the same date."],
                    ['q' => 'How do I cancel my subscription?', 'a' => "Go to Profile → Settings → Subscription → Cancel. Growth features remain active until end of your billing period. No further charges."],
                    ['q' => 'Can I get a refund?', 'a' => "Refunds are considered case-by-case. Email support@groupsave.app within 7 days of being charged. We honour refunds for billing errors or outages over 24 hours."],
                ],
            ],
            [
                'slug' => 'technical',
                'label' => 'Technical Issues',
                'icon' => '🛠️',
                'description' => 'Crashes, sync errors & troubleshooting',
                'color' => '#34d5a4',
                'sort_order' => 7,
                'articles' => [
                    ['q' => 'The app keeps crashing', 'a' => "Force close and reopen. Check for updates in the App Store / Play Store. Uninstall and reinstall if needed. Contact support with your device model and OS version."],
                    ['q' => "My dashboard data isn't syncing", 'a' => "Pull down to refresh. Log out and back in. If data mismatch persists after 24 hours, open a ticket with a screenshot of the incorrect data."],
                    ['q' => 'My account is suspended', 'a' => "Suspensions are triggered by suspicious activity or policy violations. Email support@groupsave.app with subject 'Suspension Appeal'. We review within 24 hours."],
                ],
            ],
            [
                'slug' => 'fraud',
                'label' => 'Fraud & Safety',
                'icon' => '🚨',
                'description' => 'Report fraud, disputes & suspicious activity',
                'color' => '#ff5f5f',
                'sort_order' => 8,
                'articles' => [
                    ['q' => 'How do I report a fraudulent group admin?', 'a' => "Email support@groupsave.app immediately. Subject: 'URGENT – Fraud Report'. Include the group name, admin email, and description. Our compliance team responds within 6 hours."],
                    ['q' => "What is GroupSave's fraud monitoring policy?", 'a' => "We employ automated suspicious activity monitoring. High-risk actions trigger manual review. All transactions are logged, timestamped, and auditable by our compliance team."],
                    ['q' => 'What if my identity was misused?', 'a' => "Contact us immediately at support@groupsave.app. We'll freeze the affected account, investigate, and work with you to restore access and resolve any financial impact."],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $articles = $categoryData['articles'];
            unset($categoryData['articles']);

            $category = FaqCategory::create($categoryData);

            foreach ($articles as $index => $article) {
                FaqArticle::create([
                    'category_id' => $category->id,
                    'question' => $article['q'],
                    'answer' => $article['a'],
                    'sort_order' => $index + 1,
                ]);
            }
        }
    }
}

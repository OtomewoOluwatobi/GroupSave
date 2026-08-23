# Growth Plan Payment Flow - Complete Success Path

## Flow Diagram

```
┌─────────────┐
│   Mobile    │
│     App     │
└──────┬──────┘
       │
       │ 1. POST /api/user/add-plan
       │    { "plan_id": "Growth Plan ID" }
       │
       ▼
┌──────────────────┐
│  Backend Server  │
│  (UserController)│
└──────┬───────────┘
       │
       │ Validates plan exists & is not free
       │ Creates Stripe Customer if needed
       │ Calls $user->checkout() with:
       │   - stripe_price_id: price_1Tq3XAGVRxfohcvD1bnz99ni
       │   - success_url: /api/billing/checkout/success
       │   - cancel_url: /api/billing/checkout/cancel
       │
       │ 2. Returns checkout session
       ▼
┌──────────────────────────────────────────┐
│ Response 200 OK                          │
│ {                                        │
│   "status": "success",                   │
│   "data": {                              │
│     "checkout_url": "https://checkout... │
│     "session_id": "cs_test_abc123..."    │
│   }                                      │
│ }                                        │
└──────┬───────────────────────────────────┘
       │
       │ 3. App stores session_id & opens checkout_url
       │    Linking.openURL(checkout_url)
       │
       ▼
┌──────────────────────────────┐
│  Stripe Checkout Page        │
│  (system browser opened)     │
└──────┬───────────────────────┘
       │
       │ 4. User enters card details
       │    Card: 4242 4242 4242 4242 (test)
       │    Expires: Any future date
       │    CVC: Any 3 digits
       │
       │ 5. User clicks "Pay"
       │
       ▼
┌──────────────────────────────┐
│  Stripe Processes Payment    │
└──────┬───────────────────────┘
       │
       │ 6. Payment successful
       │    session.payment_status = "paid"
       │    session.subscription = "sub_..."
       │
       │ 7. Stripe redirects to:
       │    /api/billing/checkout/success?session_id=cs_...
       │
       ▼
┌──────────────────────────────────────┐
│  Backend Server                      │
│  (StripeController::                 │
│   handleCheckoutSuccess)             │
└──────┬───────────────────────────────┘
       │
       │ 8. Retrieves session from Stripe API
       │    $session = Session::retrieve($sessionId)
       │    Validates session.payment_status == "paid"
       │
       ▼
┌──────────────────────────────────────┐
│ Response 200 OK                      │
│ {                                    │
│   "status": "success",               │
│   "data": {                          │
│     "payment_status": "paid",        │
│     "session_id": "cs_test_abc123...",
│     "subscription_id": "sub_...",    │
│     "customer_id": "cus_..."         │
│   }                                  │
│ }                                    │
└──────┬───────────────────────────────┘
       │
       │ ⚠️  At this point:
       │    - Payment is confirmed ✅
       │    - Plan is NOT yet activated
       │    - Need to wait for webhook
       │
       ▼
┌────────────────────────────────────────┐
│  Mobile App Detects Return             │
│  (deep link, foreground, or manual)    │
└──────┬─────────────────────────────────┘
       │
       │ 9. App retrieves stored session_id
       │    const sessionId = await 
       │      AsyncStorage.getItem(
       │        'pending_checkout_session'
       │      )
       │
       ▼
┌──────────────────────────────────────┐
│  Mobile App (optional verify step)   │
│  GET /api/billing/verify-session?    │
│      session_id=cs_test_abc123...    │
└──────┬───────────────────────────────┘
       │
       │ 10. Response:
       │ {
       │   "status": "success",
       │   "data": {
       │     "payment_status": "paid", ✅
       │     "session_id": "cs_...",
       │     "subscription_id": "sub_...",
       │     "customer_id": "cus_..."
       │   }
       │ }
       │
       │ 11. App shows success message
       │     "Payment successful! Growth plan activated."
       │
       ▼
┌────────────────────────────────────┐
│  Meanwhile: Stripe Webhook         │
│  (server-side, app doesn't wait)   │
└──────┬─────────────────────────────┘
       │
       │ 12. Stripe sends webhook event:
       │     POST /api/billing/webhook
       │     Event: checkout.session.completed
       │     Payload includes session_id
       │
       ▼
┌──────────────────────────────────────┐
│  Backend Server                      │
│  (WebhookController)                 │
└──────┬───────────────────────────────┘
       │
       │ 13. Webhook handler:
       │     - Verifies signature
       │     - Extracts session from payload
       │     - Looks up user & plan from metadata
       │     - Calls:
       │       $entitlements->activatePlan(
       │         $user, $plan, $expiration
       │       )
       │     - Sends PlanActivatedNotification
       │     - Stores payment record
       │
       ▼
┌──────────────────────────────────┐
│  User Plan Activated ✅          │
│  - Growth benefits unlocked      │
│  - Billing period starts         │
│  - User dashboard updates        │
│  - Notification sent to user     │
└──────────────────────────────────┘
```

---

## Detailed Step-by-Step

### Step 1: Initiate Payment (Mobile App → Backend)

**Request:**
```bash
curl -X POST https://api.rukuni.app/api/user/add-plan \
  -H "Authorization: Bearer eyJ0eXAi..." \
  -H "Content-Type: application/json" \
  -d '{
    "plan_id": "01a01926-6924-726c-a4f1-d478d3299053"
  }'
```

**Backend Processing:**
```php
// UserController::addPlan()
$plan = Plan::find('01a01926-6924-726c-a4f1-d478d3299053');
// Plan exists ✓
// billing = "monthly" ✓
// stripe_price_id = "price_1Tq3XAGVRxfohcvD1bnz99ni" ✓

// Create Stripe customer if needed
if (!$user->hasStripeId()) {
    $user->createAsStripeCustomer();
}

// Create checkout session
$session = $user->checkout(
    [], // No line items - using price ID directly
    [
        'line_items' => [
            [
                'price' => 'price_1Tq3XAGVRxfohcvD1bnz99ni',
                'quantity' => 1,
            ]
        ],
        'success_url' => 'https://api.rukuni.app/api/billing/checkout/success',
        'cancel_url' => 'https://api.rukuni.app/api/billing/checkout/cancel',
        'customer_email' => $user->email,
        'metadata' => [
            'user_id' => $user->id,
            'plan_id' => $plan->id,
        ]
    ]
);
```

**Response (200 OK):**
```json
{
  "status": "success",
  "message": "Checkout session created. Redirect user to complete payment.",
  "data": {
    "checkout_url": "https://checkout.stripe.com/pay/cs_test_a1234567890abcdefghijk",
    "session_id": "cs_test_a1234567890abcdefghijk"
  }
}
```

---

### Step 2: Open Checkout (Mobile App)

**Mobile App Code:**
```javascript
// After receiving response from Step 1
const { checkout_url, session_id } = response.data.data;

// Store session ID for later verification
await AsyncStorage.setItem('pending_checkout_session', session_id);

// Open Stripe checkout in system browser
Linking.openURL(checkout_url);
```

**What happens:**
- System browser opens
- User sees Stripe payment form
- URL: `https://checkout.stripe.com/pay/cs_test_a1234567890...`

---

### Step 3: User Completes Payment (Stripe)

**What user sees:**
```
┌─────────────────────────────────────┐
│  Stripe Checkout                    │
│                                     │
│  Growth Plan - £9.99/month         │
│                                     │
│  Card Number: [4242 4242 4242 4242]│
│  Expires:     [12/25]              │
│  CVC:         [123]                │
│                                     │
│  [Pay £9.99]                        │
└─────────────────────────────────────┘
```

**What Stripe does:**
- Processes card
- Marks session as `payment_status = "paid"`
- Creates subscription: `sub_1234567890abcd`
- Redirects to success_url with session_id

---

### Step 4: Stripe Redirects to Success URL

**Browser navigates to:**
```
GET https://api.rukuni.app/api/billing/checkout/success?session_id=cs_test_a1234567890...
```

**⚠️ IMPORTANT:** This response goes to the **BROWSER**, not the mobile app!

The browser displays:
```
✓ Payment Successful

Your payment was successful! Your plan is being activated.
You can close this window and return to the app.

Session ID: cs_test_a1234567890...

[Close Window]
```

Then the browser window **auto-closes after 5 seconds** or user clicks "Close Window".

**The mobile app does NOT see this response.** It just sees that the browser closed and the user returned to the app.


---

### Step 5: Mobile App Detects Return & Verifies Payment

**How the app knows to verify:**
- Browser window closed (user returned to app foreground)
- OR deep link was triggered (if configured)
- OR app detects it was backgrounded during checkout

**The app uses the session_id it STORED in Step 1**, not from the browser response (which it never saw).

**Verify Request (using stored session_id):**
```bash
curl -X GET "https://api.rukuni.app/api/user/billing/verify-session?session_id=cs_test_a1234567890..." \
  -H "Authorization: Bearer eyJ0eXAi..."
```

**Backend Processes:**
```php
// StripeController::verifyCheckoutSession()
$sessionId = $request->query('session_id'); // cs_test_a1234567890...

// Call Stripe API directly (NOT dependent on browser)
$session = \Stripe\Checkout\Session::retrieve($sessionId);

return [
    'payment_status' => $session->payment_status,    // "paid", "unpaid", "no_payment_required"
    'session_id' => $session->id,
    'subscription_id' => $session->subscription,
    'customer_id' => $session->customer,
];
```

**Verify Response:**
```json
{
  "status": "success",
  "data": {
    "payment_status": "paid",
    "session_id": "cs_test_a1234567890...",
    "subscription_id": "sub_1234567890abcd",
    "customer_id": "cus_1234567890abcd"
  }
}
```

---

### Step 6: Mobile App Shows Success (Based on verification)

```javascript
const verifyCheckoutSession = async (sessionId) => {
  try {
    const response = await fetch(
      `https://api.rukuni.app/api/user/billing/verify-session?session_id=${sessionId}`,
      {
        headers: { 'Authorization': `Bearer ${token}` }
      }
    );
    
    const data = await response.json();
    
    if (data.data.payment_status === 'paid') {
      // ✅ Payment successful
      showAlert('Success!', 'Your Growth plan is now active!');
      
      // Refresh dashboard
      await refreshUserDashboard();
      
      // Clear stored session
      await AsyncStorage.removeItem('pending_checkout_session');
      
      // Navigate to dashboard
      navigation.navigate('Dashboard');
    } else {
      // ⏳ Still processing
      showAlert('Payment Pending', 'Your payment is being processed...');
    }
  } catch (error) {
    showAlert('Error', 'Failed to verify payment. Try again.');
  }
};
```

---

### Step 7: Stripe Webhook Fires (Server-side, no app involvement)

**Stripe sends webhook:**
```
POST https://api.rukuni.app/api/billing/webhook
Content-Type: application/json

{
  "id": "evt_1234567890abcdef",
  "type": "checkout.session.completed",
  "data": {
    "object": {
      "id": "cs_test_a1234567890...",
      "payment_status": "paid",
      "subscription": "sub_1234567890abcd",
      "customer": "cus_1234567890abcd",
      "metadata": {
        "user_id": "01a01926-6869-70f7-84eb-98c6c79c945a",
        "plan_id": "01a01926-6924-726c-a4f1-d478d3299053"
      }
    }
  }
}
```

**Backend Webhook Handler:**
```php
// WebhookController::handleCheckoutSessionCompleted()

// 1. Verify webhook signature
\Stripe\Webhook::constructEvent($body, $sig_header, $webhook_secret);

// 2. Extract session
$session = $event->data->object;

// 3. Lookup user and plan from metadata
$user = User::find($session->metadata->user_id);
$plan = Plan::find($session->metadata->plan_id);

// 4. Activate plan with subscription start date
$expiration = null; // Will be calculated based on Stripe billing cycle
$this->entitlements->activatePlan($user, $plan, $expiration);

// 5. Send notification
NotificationService::send($user, new PlanActivatedNotification($user->name, $plan->name));

// 6. Log success
Log::info('Plan activated via webhook', [
    'user_id' => $user->id,
    'plan_id' => $plan->id,
    'subscription_id' => $session->subscription,
]);

// 7. Return 200 OK to Stripe
return response()->json(['status' => 'received'], 200);
```

---

### Step 8: User Dashboard Updates

**What user can now do:**
- ✅ Access Growth plan features
- ✅ See "Active: Growth Plan" on dashboard
- ✅ See billing date for next renewal
- ✅ See subscription details
- ✅ Receive plan-specific benefits

**What happened in database:**
```sql
-- Plans table (unchanged)
SELECT * FROM plans WHERE slug = 'growth';
-- stripe_price_id: price_1Tq3XAGVRxfohcvD1bnz99ni ✓

-- User Plans table (NEW)
INSERT INTO user_plans (user_id, plan_id, starts_at, ends_at, stripe_subscription_id)
VALUES (
  '01a01926-6869-70f7-84eb-98c6c79c945a',
  '01a01926-6924-726c-a4f1-d478d3299053',
  '2026-08-23 14:30:00',
  null, -- Recurring, no end date
  'sub_1234567890abcd'
);

-- Stripe Subscriptions table (NEW)
INSERT INTO stripe_subscriptions (
  user_id, stripe_id, stripe_plan_id, status, 
  current_period_start, current_period_end
) VALUES (
  '01a01926-6869-70f7-84eb-98c6c79c945a',
  'sub_1234567890abcd',
  'price_1Tq3XAGVRxfohcvD1bnz99ni',
  'active',
  '2026-08-23 14:30:00',
  '2026-09-23 14:30:00'
);
```

---

## Success Indicators

✅ **Payment Flow is Successful When:**

| Step | Indicator | How to Verify |
|------|-----------|---------------|
| 1 | Checkout session created | Response has `checkout_url` and `session_id` |
| 2 | User opens Stripe | Browser shows Stripe payment form |
| 3 | User completes payment | Stripe redirects automatically |
| 4 | Stripe confirms payment | Success page shows `payment_status: "paid"` |
| 5 | App detects return | verify-session endpoint returns `paid` status |
| 6 | App shows success | User sees "Growth plan activated" message |
| 7 | Webhook fires | Check Stripe dashboard webhook logs → `succeeded` |
| 8 | Database updated | `user_plans` table has new record with Growth plan |

---

## Testing This Flow

### Test Card (Stripe Test Mode)
```
Number:  4242 4242 4242 4242
Expires: 12/25 (any future date)
CVC:     123 (any 3 digits)
```

### Full Test Sequence
```bash
# 1. Login and get token
curl -X POST https://api.rukuni.app/api/auth/login \
  -d '{"email":"test@example.com","password":"password"}'
# Response: { "token": "eyJ0eXAi..." }

# 2. Initiate checkout
curl -X POST https://api.rukuni.app/api/user/add-plan \
  -H "Authorization: Bearer eyJ0eXAi..." \
  -H "Content-Type: application/json" \
  -d '{"plan_id":"01a01926-6924-726c-a4f1-d478d3299053"}'
# Response: { "checkout_url": "...", "session_id": "cs_test_..." }

# 3. Open checkout_url in browser, pay with 4242 card
# → Stripe redirects to success URL
# → Check browser console for payment_status: "paid"

# 4. Verify payment (if not done via deep link)
curl -X GET "https://api.rukuni.app/api/user/billing/verify-session?session_id=cs_test_..." \
  -H "Authorization: Bearer eyJ0eXAi..."
# Response: { "payment_status": "paid", "subscription_id": "sub_..." }

# 5. Check webhook logs in Stripe dashboard
# → Event: checkout.session.completed
# → Status: Sent / Succeeded

# 6. Verify plan is active on dashboard
curl -X GET https://api.rukuni.app/api/user/dashboard \
  -H "Authorization: Bearer eyJ0eXAi..."
# Response should include: "current_plan": { "name": "Growth", ... }
```

---

## Common Issues & Fixes

| Issue | Cause | Fix |
|-------|-------|-----|
| "No such price: 'prod_xyz'" | Database has Product ID instead of Price ID | Update database: `UPDATE plans SET stripe_price_id = 'price_1Tq3XAGVRxfohcvD1bnz99ni' WHERE slug = 'growth'` |
| Session ID not returned | Code not updated | Ensure `StripeController` returns `session_id` in response |
| verify-session returns "unpaid" | Payment still processing | Wait 5-10 seconds and retry (Stripe eventually updates) |
| Webhook doesn't fire | Webhook URL not configured | Check Stripe dashboard → Webhooks → Ensure URL is `https://api.rukuni.app/api/billing/webhook` |
| Plan not active after payment | Webhook didn't trigger or failed | Check Stripe webhook logs, manually run webhook handler |
| Deep link doesn't work | Deep link handler not registered | Use app foreground detection instead |


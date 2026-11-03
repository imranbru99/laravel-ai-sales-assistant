<?php

namespace App\Core\Ai;

use App\Models\Lead;
use App\Models\Deal;

class DemoAiDriver
{
    public function scoreLead(Lead $lead): array
    {
        $budget = (float) ($lead->budget ?? 0);
        $hasEmail = !empty($lead->email);
        $hasPhone = !empty($lead->phone);
        $hasCompany = !empty($lead->company_name);

        // Rule score (0 - 40)
        $ruleScore = 10;
        if ($hasEmail && $hasPhone) $ruleScore += 10;
        if ($hasCompany) $ruleScore += 10;
        if ($budget >= 10000) $ruleScore += 10;
        elseif ($budget >= 3000) $ruleScore += 5;

        // Engagement score (0 - 30)
        $engagementScore = 15;
        if ($lead->last_contacted_at && $lead->last_contacted_at->diffInDays(now()) <= 3) {
            $engagementScore += 15;
        } elseif ($lead->status === 'qualified') {
            $engagementScore += 10;
        }

        // AI Qualitative Fit Score (0 - 30)
        $aiScore = match ($lead->source) {
            'referral' => 28,
            'website' => 24,
            'campaign' => 20,
            'ad' => 18,
            default => 15,
        };

        $totalScore = min(100, max(10, $ruleScore + $engagementScore + $aiScore));

        $grade = match (true) {
            $totalScore >= 80 => 'A',
            $totalScore >= 65 => 'B',
            $totalScore >= 50 => 'C',
            default => 'D',
        };

        $explanations = [
            "Strong demographic match: verified {$lead->source} inbound source with budget of \${$budget}.",
            $hasCompany ? "Enterprise domain detected for '{$lead->company_name}' with high decision-maker authority." : "Individual professional account.",
            $grade === 'A' ? 'High buying intent observed with active engagement within last 72 hours.' : 'Moderate buyer readiness; requires consultative nurturing.',
        ];

        $actions = match ($grade) {
            'A' => 'Urgent: Schedule a live solution walkthrough within 24 hours. Send enterprise case study.',
            'B' => 'Send personalized value proposal highlighting ROI and schedule a discovery call.',
            'C' => 'Enroll in 3-part educational drip sequence to identify primary pain points.',
            default => 'Verify phone contact and request budget confirmation.',
        };

        return [
            'score' => $totalScore,
            'grade' => $grade,
            'rule_score' => $ruleScore,
            'engagement_score' => $engagementScore,
            'ai_score' => $aiScore,
            'explanation' => implode(' ', $explanations),
            'recommended_action' => $actions,
            'breakdown' => [
                'fit_factors' => ['verified_channel' => true, 'budget_qualified' => $budget > 3000],
                'behavior_signals' => ['recent_touch' => true],
            ],
        ];
    }

    public function draftFollowUp(Lead|Deal $record, array $options = []): array
    {
        $channel = $options['channel'] ?? 'email';
        $tone = $options['tone'] ?? 'consultative';
        $language = $options['language'] ?? 'en';

        $recipientName = ($record instanceof Lead) ? $record->contact_name : ($record->contact?->full_name ?? 'Valued Client');
        $companyName = ($record instanceof Lead) ? ($record->company_name ?? 'your organization') : ($record->company?->name ?? 'your company');

        if ($language === 'bn') {
            $subject = "{$companyName}-এর জন্য বিক্রয় বৃদ্ধি ও অটোমেশন প্রস্তাবনা";
            $content = match ($channel) {
                'whatsapp' => "আসসালামু আলাইকুম {$recipientName} সাহেব, {$companyName}-এর জন্য আমাদের এআই সেলস অ্যাসিস্ট্যান্ট প্ল্যাটফর্ম নিয়ে আলোচনা করতে পেরে ভালো লাগলো। আপনার সুবিধাজনক সময়ে একটি ১৫ মিনিটের ডেমো দেখতে পারেন কি? ধন্যবাদ।",
                'sms' => "{$recipientName}, {$companyName}-এর সেলস ফলো-আপ অটোমেশনের বিস্তারিত পাঠানো হয়েছে। যেকোনো প্রয়োজনে কল করুন।",
                default => "প্রিয় {$recipientName},\n\nআশা করি ভালো আছেন। {$companyName}-এর বিক্রয় প্রক্রিয়াকে আরও গতিশীল ও স্বয়ংক্রিয় করতে আমাদের আলোচনা এগিয়ে নিতে আগ্রহী।\n\nআমরা আপনার দলের জন্য একটি বিশেষ সমাধান তৈরি করেছি যা ফলো-আপ এবং লিড স্কোরিং প্রক্রিয়াকে ১০০% স্বয়ংক্রিয় করবে।\n\nআগামীকাল অথবা পরশু কি আপনার সাথে ২০ মিনিটের একটি ভার্চুয়াল মিটিং করা সম্ভব?\n\nশুভকামনায়,\nসেলস টিম",
            };
        } else {
            $subject = "Accelerating sales velocity for {$companyName}";
            $content = match ($channel) {
                'whatsapp' => "Hi {$recipientName}! Following up on our recent chat regarding {$companyName}'s sales pipeline automation. Would you have 10 minutes this Thursday for a quick live walkthrough? Let me know what time works best!",
                'sms' => "Hi {$recipientName}, tailored follow-up ideas for {$companyName} are ready for review. Check your email or reply here to connect. Thanks!",
                'call' => "Phone Script:\n1. Intro: 'Hi {$recipientName}, this is following up on your inquiry for {$companyName}.'\n2. Value Hook: 'I noticed your team is expanding outreach, and wanted to share how we helped similar firms cut lead response time by 80%.'\n3. Call-to-action: 'Do you have 5 minutes to look at two quick benchmarks?'",
                default => "Hi {$recipientName},\n\nI hope your week is off to a productive start.\n\nFollowing up on our recent conversation about scaling sales operations at {$companyName}, I wanted to share a quick insight on how our AI Sales Assistant platform automatically drafts follow-ups and prioritizes Grade A opportunities for teams like yours.\n\nBased on your current volume, implementing automated lead scoring could save your sales executives 8+ hours per week while improving conversion rates by up to 35%.\n\nWould you be open to a brief 15-minute walkthrough this Wednesday or Thursday afternoon?\n\nBest regards,\nSales Advisory Team",
            };
        }

        return [
            'subject' => $subject,
            'content' => $content,
            'channel' => $channel,
            'tone' => $tone,
            'language' => $language,
        ];
    }

    public function askCopilot(string $query, $tenant, $user): string
    {
        $queryLower = strtolower($query);

        if (str_contains($queryLower, 'lead') || str_contains($queryLower, 'hot')) {
            $hotLeadCount = Lead::where('score_grade', 'A')->count();
            $totalLeads = Lead::count();
            $tenantName = $tenant ? $tenant->name : 'your business';
            return "Based on {$tenantName}'s CRM data, you currently have **{$hotLeadCount} Grade A (Hot) leads** out of **{$totalLeads} total prospects**. I recommend prioritizing follow-ups for leads with an SLA countdown under 24 hours. Would you like me to draft batch WhatsApp reminders for them?";
        }

        if (str_contains($queryLower, 'pipeline') || str_contains($queryLower, 'deal') || str_contains($queryLower, 'revenue')) {
            $dealCount = Deal::where('status', 'open')->count();
            $totalValue = Deal::where('status', 'open')->sum('value');
            return "Your active pipeline currently holds **{$dealCount} open opportunities** valued at **\${$totalValue}**. 2 deals have been stagnant for more than 7 days in the 'Proposal Sent' stage and are flagged as at-risk of rotting.";
        }

        return "I analyzed your account for '{$query}'. All data is strictly isolated to tenant **{$tenant->name}**. Your sales activity velocity is up 18% this month, with an average lead response time of 32 minutes.";
    }

    public function enrichCompany(string $urlOrName): array
    {
        $domain = parse_url($urlOrName, PHP_URL_HOST) ?? $urlOrName;
        return [
            'domain' => $domain,
            'industry' => 'Enterprise Technology & Services',
            'employee_range' => '51-200',
            'tech_stack' => ['Laravel', 'TailwindCSS', 'MySQL', 'Redis', 'AWS'],
            'socials' => [
                'linkedin' => "https://linkedin.com/company/" . str_replace('.', '', $domain),
                'twitter' => "https://twitter.com/" . str_replace('.', '', $domain),
            ],
            'description' => "High-growth commercial enterprise providing modern digital and B2B solutions with automated customer engagement workflows.",
        ];
    }
}

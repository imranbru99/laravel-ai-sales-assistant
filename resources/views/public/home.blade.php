<x-layouts.public title="AI Sales Assistant — Drafts Follow-ups, Scores Leads, Runs Your Whole Business">
    <!-- Hero Section -->
    <section class="relative overflow-hidden pt-12 pb-24 lg:pt-20 lg:pb-32">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(ellipse_80%_80%_at_50%_-20%,rgba(245,158,11,0.15),rgba(255,255,255,0))]"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-900 border border-amber-500/30 text-amber-400 text-xs font-semibold mb-6 shadow-sm">
                <span class="flex h-2 w-2 rounded-full bg-amber-400 animate-pulse"></span>
                <span>The Complete Modular Sales &amp; Business Operating System</span>
            </div>

            <!-- Main Heading -->
            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight text-white max-w-5xl mx-auto leading-[1.1]">
                Drafts follow-ups, scores leads, <br class="hidden sm:inline" />
                <span class="bg-gradient-to-r from-amber-400 via-orange-400 to-amber-200 bg-clip-text text-transparent">and runs your whole business.</span>
            </h1>

            <p class="mt-6 text-lg sm:text-xl text-slate-300 max-w-3xl mx-auto leading-relaxed">
                Turn cold inquiries into closed deals automatically. From boutique agencies to garment merchandisers, switch 76 enterprise modules ON or OFF to fit your exact workflow.
            </p>

            <!-- CTA Actions -->
            <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                <a href="/demo" class="px-8 py-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-base shadow-xl shadow-amber-500/25 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-950" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Explore Live Demo (8 Roles)
                </a>
                <a href="/features" class="px-8 py-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-200 font-semibold text-base border border-slate-800 transition-all hover:border-slate-700">
                    Browse All 76 Modules &rarr;
                </a>
            </div>

            <!-- Live Proof stats -->
            <div class="mt-14 grid grid-cols-2 sm:grid-cols-4 gap-6 max-w-4xl mx-auto pt-10 border-t border-slate-900 text-left">
                <div>
                    <div class="text-3xl font-extrabold text-white">0–100</div>
                    <div class="text-xs text-slate-400 mt-1">AI Lead Scoring with plain-language explanations</div>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-amber-400">&lt; 30 sec</div>
                    <div class="text-xs text-slate-400 mt-1">Multi-channel AI follow-up draft generation</div>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-white">76 Modules</div>
                    <div class="text-xs text-slate-400 mt-1">Toggle ON/OFF without code changes</div>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-indigo-400">11 Presets</div>
                    <div class="text-xs text-slate-400 mt-1">1-click setups for retail, garment, clinic, B2B</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive AI Lead Scoring Simulator -->
    <section id="lead-scoring" class="py-20 bg-slate-900/50 border-y border-slate-800/80" x-data="{
        budget: 50000,
        source: 'referral',
        hasDomain: true,
        urgency: 'high',
        get score() {
            let base = 40;
            if (this.budget > 40000) base += 25;
            else if (this.budget > 10000) base += 15;
            if (this.source === 'referral') base += 20;
            if (this.source === 'inbound') base += 12;
            if (this.hasDomain) base += 10;
            if (this.urgency === 'high') base += 15;
            return Math.min(100, base);
        },
        get grade() {
            if (this.score >= 85) return 'A';
            if (this.score >= 70) return 'B';
            if (this.score >= 50) return 'C';
            return 'D';
        },
        get explanation() {
            if (this.grade === 'A') return 'High-priority enterprise prospect. Verified corporate domain, robust budget exceeding $40k, strong referral origin, and rapid buying timeline.';
            if (this.grade === 'B') return 'Solid potential deal. Good qualification signals across budget and source; schedule a technical discovery call within 24 hours.';
            if (this.grade === 'C') return 'Nurture candidate. Moderate budget readiness; enroll in educational email sequence before direct sales rep outreach.';
            return 'Low current fit. Limited budget readiness or generic web inbox inquiry. Keep in marketing newsletter.';
        },
        get nextAction() {
            if (this.grade === 'A') return 'Send VIP customized quotation & book Executive Demo call.';
            if (this.grade === 'B') return 'Send consultative follow-up email with relevant case study.';
            if (this.grade === 'C') return 'Enroll in 5-step automated value nurturing sequence.';
            return 'Send self-service catalog link and monthly digest.';
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="text-xs uppercase tracking-widest font-bold text-amber-400">Core Signature Feature #1</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-white mt-2">Interactive AI Lead Scoring Simulator</h2>
                <p class="text-slate-400 mt-3 text-sm">
                    Move the sliders below to test how our AI Lead Scoring Engine evaluates leads in real time combining demographic, behavioral, and AI signals.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Inputs Column -->
                <div class="lg:col-span-6 bg-slate-950 p-6 sm:p-8 rounded-2xl border border-slate-800 shadow-xl space-y-6">
                    <h3 class="text-base font-semibold text-white flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                        Adjust Qualification Signals
                    </h3>

                    <!-- Budget Slider -->
                    <div>
                        <div class="flex justify-between text-xs font-medium text-slate-300 mb-2">
                            <span>Project Budget</span>
                            <span class="text-amber-400 font-bold" x-text="'$' + budget.toLocaleString()"></span>
                        </div>
                        <input type="range" min="1000" max="100000" step="5000" x-model.number="budget" class="w-full accent-amber-500 bg-slate-800 rounded-lg cursor-pointer">
                    </div>

                    <!-- Source -->
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-2">Lead Origin / Channel</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" @click="source = 'referral'" :class="source === 'referral' ? 'bg-amber-500/20 text-amber-400 border-amber-500/50' : 'bg-slate-900 text-slate-400 border-slate-800'" class="px-3 py-2 text-xs rounded-lg border font-medium transition-all">
                                Referral / VIP
                            </button>
                            <button type="button" @click="source = 'inbound'" :class="source === 'inbound' ? 'bg-amber-500/20 text-amber-400 border-amber-500/50' : 'bg-slate-900 text-slate-400 border-slate-800'" class="px-3 py-2 text-xs rounded-lg border font-medium transition-all">
                                Inbound Form
                            </button>
                            <button type="button" @click="source = 'cold'" :class="source === 'cold' ? 'bg-amber-500/20 text-amber-400 border-amber-500/50' : 'bg-slate-900 text-slate-400 border-slate-800'" class="px-3 py-2 text-xs rounded-lg border font-medium transition-all">
                                Cold Outreach
                            </button>
                        </div>
                    </div>

                    <!-- Verified Domain Toggle -->
                    <div class="flex items-center justify-between pt-2">
                        <span class="text-xs font-medium text-slate-300">Corporate Business Domain (.com/.io vs free webmail)</span>
                        <button type="button" @click="hasDomain = !hasDomain" :class="hasDomain ? 'bg-amber-500' : 'bg-slate-800'" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                            <span :class="hasDomain ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                        </button>
                    </div>

                    <!-- Buying Urgency -->
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-2">Decision Timeline</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" @click="urgency = 'high'" :class="urgency === 'high' ? 'bg-amber-500/20 text-amber-400 border-amber-500/50' : 'bg-slate-900 text-slate-400 border-slate-800'" class="px-3 py-2 text-xs rounded-lg border font-medium transition-all">
                                Within 30 Days (High)
                            </button>
                            <button type="button" @click="urgency = 'evaluating'" :class="urgency === 'evaluating' ? 'bg-amber-500/20 text-amber-400 border-amber-500/50' : 'bg-slate-900 text-slate-400 border-slate-800'" class="px-3 py-2 text-xs rounded-lg border font-medium transition-all">
                                Just Researching (Low)
                            </button>
                        </div>
                    </div>
                </div>

                <!-- AI Output Preview Column -->
                <div class="lg:col-span-6 bg-gradient-to-br from-slate-950 to-slate-900 p-6 sm:p-8 rounded-2xl border border-slate-800 shadow-2xl relative overflow-hidden">
                    <div class="flex items-center justify-between pb-6 border-b border-slate-800">
                        <div>
                            <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold">AI Lead Score</span>
                            <div class="flex items-baseline gap-3 mt-1">
                                <span class="text-5xl font-black text-white" x-text="score"></span>
                                <span class="text-slate-500 font-medium">/ 100</span>
                            </div>
                        </div>

                        <!-- Grade Badge -->
                        <div class="h-16 w-16 rounded-2xl flex flex-col items-center justify-center font-black text-2xl border" :class="{
                            'bg-emerald-500/20 text-emerald-400 border-emerald-500/40': grade === 'A',
                            'bg-blue-500/20 text-blue-400 border-blue-500/40': grade === 'B',
                            'bg-amber-500/20 text-amber-400 border-amber-500/40': grade === 'C',
                            'bg-rose-500/20 text-rose-400 border-rose-500/40': grade === 'D'
                        }">
                            <span class="text-[10px] uppercase tracking-widest font-normal text-slate-400">Grade</span>
                            <span x-text="grade"></span>
                        </div>
                    </div>

                    <!-- AI Explanation -->
                    <div class="mt-6 space-y-4">
                        <div>
                            <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">AI Analysis &amp; Reasoning</span>
                            <p class="text-sm text-slate-300 mt-1 leading-relaxed bg-slate-900/80 p-4 rounded-xl border border-slate-800/80" x-text="explanation"></p>
                        </div>

                        <div>
                            <span class="text-xs font-bold text-indigo-400 uppercase tracking-wider">Recommended Next Action</span>
                            <div class="mt-1 flex items-center gap-3 bg-indigo-950/40 border border-indigo-500/30 p-3 rounded-xl text-indigo-200 text-sm font-medium">
                                <svg class="w-5 h-5 text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                                <span x-text="nextAction"></span>
                            </div>
                        </div>

                        <div class="pt-4 flex items-center justify-between text-xs text-slate-400 border-t border-slate-800">
                            <span>Powered by <strong>laravel-ai-hub</strong></span>
                            <a href="/demo" class="text-amber-400 hover:underline font-semibold">Test in Live CRM &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive AI Follow-up Assistant Teaser -->
    <section id="follow-up" class="py-20 bg-slate-950" x-data="{
        channel: 'email',
        tone: 'consultative',
        leadName: 'David Sterling',
        company: 'Sterling Global Logix',
        get draft() {
            if (this.channel === 'whatsapp') {
                return 'Hi ' + this.leadName + ' 👋 Just reviewed your logistics spec for ' + this.company + '. I drafted our turnaround timeline and quote #QT-2026. Can I send the PDF over here or prefer a 5-min WhatsApp call?';
            }
            if (this.channel === 'sms') {
                return 'Hi ' + this.leadName + ', Imran here from AI Sales. Your customized proposal for ' + this.company + ' is ready for review: https://novatrading.com/p/8k2. Let me know if any questions!';
            }
            if (this.channel === 'linkedin') {
                return 'Hi ' + this.leadName + ', enjoyed reviewing ' + this.company + '\'s supply chain expansion. Prepared an executive summary on how automated scoring + follow-ups reduced cycle time by 42%. Would love to share insights!';
            }
            // Email
            return 'Subject: Customized follow-up regarding ' + this.company + '\'s commercial expansion\n\nDear ' + this.leadName + ',\n\nFollowing up on our recent discussion regarding your pipeline automation goals. I have prepared a detailed proposal addressing your delivery timelines and multi-branch requirements.\n\nKey next steps:\n1. Review the attached Unicode PDF estimate\n2. Schedule a 15-minute alignment call this Thursday\n\nLooking forward to your thoughts.\n\nWarm regards,\nImran Ahmed\nAI Sales Assistant';
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="text-xs uppercase tracking-widest font-bold text-amber-400">Core Signature Feature #2</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-white mt-2">Multichannel AI Follow-up Drafter</h2>
                <p class="text-slate-400 mt-3 text-sm">
                    Sales reps spend hours writing emails. Our AI analyzes full deal history, brand voice, and recent notes to draft the next follow-up in seconds.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Controls -->
                <div class="lg:col-span-5 bg-slate-900/60 p-6 rounded-2xl border border-slate-800 space-y-6">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-2">Select Channel</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" @click="channel = 'email'" :class="channel === 'email' ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-slate-950 text-slate-300 border-slate-800'" class="p-3 rounded-xl border text-xs text-left transition-all">
                                ✉️ Email (Long Form)
                            </button>
                            <button type="button" @click="channel = 'whatsapp'" :class="channel === 'whatsapp' ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-slate-950 text-slate-300 border-slate-800'" class="p-3 rounded-xl border text-xs text-left transition-all">
                                💬 WhatsApp Chat
                            </button>
                            <button type="button" @click="channel = 'linkedin'" :class="channel === 'linkedin' ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-slate-950 text-slate-300 border-slate-800'" class="p-3 rounded-xl border text-xs text-left transition-all">
                                💼 LinkedIn InMail
                            </button>
                            <button type="button" @click="channel = 'sms'" :class="channel === 'sms' ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-slate-950 text-slate-300 border-slate-800'" class="p-3 rounded-xl border text-xs text-left transition-all">
                                📱 SMS Text
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-2">Brand Voice Tone</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" @click="tone = 'consultative'" :class="tone === 'consultative' ? 'border-amber-500 text-amber-400 bg-amber-500/10' : 'border-slate-800 text-slate-400'" class="py-2 text-xs border rounded-lg font-medium">
                                Consultative
                            </button>
                            <button type="button" @click="tone = 'friendly'" :class="tone === 'friendly' ? 'border-amber-500 text-amber-400 bg-amber-500/10' : 'border-slate-800 text-slate-400'" class="py-2 text-xs border rounded-lg font-medium">
                                Friendly
                            </button>
                            <button type="button" @click="tone = 'urgent'" :class="tone === 'urgent' ? 'border-amber-500 text-amber-400 bg-amber-500/10' : 'border-slate-800 text-slate-400'" class="py-2 text-xs border rounded-lg font-medium">
                                Urgent
                            </button>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-800 text-xs text-slate-400 space-y-2">
                        <div class="flex items-center gap-2 text-slate-300">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Human-in-the-loop review or 1-click approve</span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-300">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Multi-language drafting (English, Bangla, Arabic)</span>
                        </div>
                    </div>
                </div>

                <!-- Live Draft Display -->
                <div class="lg:col-span-7 bg-slate-950 p-6 sm:p-8 rounded-2xl border border-slate-800 shadow-2xl relative">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-800 text-xs text-slate-400">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                            <span>Draft for: <strong class="text-white" x-text="leadName + ' (' + company + ')'"></strong></span>
                        </div>
                        <span class="text-amber-400 font-medium" x-text="'Channel: ' + channel.toUpperCase()"></span>
                    </div>

                    <pre class="mt-4 font-sans text-sm text-slate-200 whitespace-pre-wrap leading-relaxed bg-slate-900/50 p-4 rounded-xl border border-slate-800/80 min-h-[220px]" x-text="draft"></pre>

                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-slate-800">
                        <span class="text-xs text-slate-500">Approvals tracked in Audit Log</span>
                        <div class="flex items-center gap-2">
                            <a href="/demo" class="px-4 py-2 rounded-lg bg-amber-500 text-slate-950 font-semibold text-xs hover:bg-amber-400">
                                Send in CRM Demo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Modules Grid -->
    <section class="py-20 bg-slate-900/40 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
                <div>
                    <span class="text-xs uppercase tracking-widest font-bold text-amber-400">The Power of Modularity</span>
                    <h2 class="text-3xl sm:text-4xl font-bold text-white mt-2">76 Specialized Modules</h2>
                    <p class="text-slate-400 mt-2 text-sm">Every capability is an independent module. Turn them ON or OFF anytime.</p>
                </div>
                <a href="/features" class="mt-4 md:mt-0 text-amber-400 hover:text-amber-300 font-semibold text-sm flex items-center gap-1">
                    Explore all 76 modules &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($featuredModules as $key => $mod)
                    <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800 hover:border-amber-500/40 transition-all flex flex-col justify-between group">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[10px] uppercase tracking-wider font-semibold px-2 py-0.5 rounded-full bg-slate-800 text-amber-400 border border-slate-700">
                                    {{ $mod['category'] }}
                                </span>
                                @if(!empty($mod['price_monthly']))
                                    <span class="text-xs text-slate-400">${{ $mod['price_monthly'] }}/mo</span>
                                @endif
                            </div>
                            <h3 class="text-lg font-bold text-white group-hover:text-amber-400 transition-colors">
                                {{ $mod['name'] }}
                            </h3>
                            <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                                {{ $mod['description'] }}
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-900 flex items-center justify-between">
                            <span class="text-xs text-slate-500">{{ count($mod['features'] ?? []) }} features</span>
                            <a href="/features/{{ Str::slug($mod['name']) }}" class="text-xs font-semibold text-amber-400 hover:underline">
                                Learn more &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Business Presets Section -->
    <section class="py-20 bg-slate-950 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="text-xs uppercase tracking-widest font-bold text-amber-400">Ready For Any Business</span>
                <h2 class="text-3xl sm:text-4xl font-bold text-white mt-2">11 Pre-Packaged Business Presets</h2>
                <p class="text-slate-400 mt-3 text-sm">
                    Don't want to configure 76 modules manually? Apply a preset with one click.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($presets as $pkey => $preset)
                    <div class="bg-slate-900/60 p-6 rounded-2xl border border-slate-800 hover:border-slate-700 transition-all flex flex-col justify-between">
                        <div>
                            <h3 class="text-base font-bold text-white mb-2">{{ $preset['name'] }}</h3>
                            <p class="text-xs text-slate-400 leading-relaxed mb-4">{{ $preset['description'] }}</p>
                            <div class="text-[11px] text-slate-500">
                                <strong>{{ count($preset['modules']) }} modules</strong> included
                            </div>
                        </div>
                        <a href="/solutions/{{ $pkey }}" class="mt-6 inline-block text-xs font-semibold text-amber-400 hover:underline">
                            View Preset Details &rarr;
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Demo Personas Callout -->
    <section class="py-20 bg-gradient-to-br from-amber-600/10 via-slate-950 to-indigo-600/10 border-t border-slate-800 text-center">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl sm:text-4xl font-bold text-white">Experience AI Sales Assistant Live</h2>
            <p class="text-slate-300 mt-4 text-base max-w-2xl mx-auto">
                No sign-up form, no waiting. Choose any of our 8 demo personas — Super Admin, Managing Director, Sales Rep, Finance Manager, or Client — and explore with one click.
            </p>
            <div class="mt-8">
                <a href="/demo" class="px-8 py-4 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-base shadow-xl shadow-amber-500/20 inline-flex items-center gap-2">
                    Open Demo Switcher (8 Personas) &rarr;
                </a>
            </div>
        </div>
    </section>
</x-layouts.public>

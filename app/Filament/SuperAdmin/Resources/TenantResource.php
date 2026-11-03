<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\TenantResource\Pages;
use App\Models\ImpersonationLog;
use App\Models\Tenant;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Platform Tenancy';
    protected static ?string $navigationLabel = 'Tenants (Businesses)';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Business Identity')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('domain')
                            ->placeholder('customdomain.com')
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('business_type')
                            ->options([
                                'agency' => 'B2B Agency / Services',
                                'retail' => 'Small Retail Shop',
                                'wholesale' => 'Wholesale / Distribution',
                                'garments' => 'Garment / Merchandiser',
                                'real_estate' => 'Real Estate',
                                'education' => 'Education / Coaching',
                                'clinic' => 'Clinic / Healthcare',
                                'ngo' => 'NGO / Non-profit',
                                'saas' => 'SaaS / Software',
                                'ecommerce' => 'E-Commerce Brand',
                                'freelancer' => 'Solo Freelancer',
                            ])
                            ->default('agency')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Subscription & Governance')
                    ->schema([
                        Forms\Components\Select::make('plan')
                            ->options([
                                'free' => 'Free Community',
                                'starter' => 'Starter ($49/mo)',
                                'growth' => 'Growth ($149/mo)',
                                'enterprise' => 'Enterprise ($399/mo)',
                            ])
                            ->default('growth')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'trial' => 'Trial Period',
                                'suspended' => 'Suspended (Policy / Non-payment)',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('active')
                            ->required(),
                        Forms\Components\DateTimePicker::make('trial_ends_at')
                            ->label('Trial Expiry Date'),
                        Forms\Components\Select::make('currency')
                            ->options([
                                'USD' => 'USD ($)',
                                'BDT' => 'BDT (৳)',
                                'EUR' => 'EUR (€)',
                                'GBP' => 'GBP (£)',
                                'INR' => 'INR (₹)',
                            ])
                            ->default('USD')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('slug')
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                Tables\Columns\TextColumn::make('business_type')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('plan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'enterprise' => 'success',
                        'growth' => 'primary',
                        'starter' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'trial' => 'info',
                        'suspended' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'trial' => 'Trial',
                        'suspended' => 'Suspended',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('plan')
                    ->options([
                        'starter' => 'Starter',
                        'growth' => 'Growth',
                        'enterprise' => 'Enterprise',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('impersonate')
                    ->label('Impersonate')
                    ->icon('heroicon-o-arrow-right-start-on-rectangle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Tenant $record) {
                        $tenantUser = User::where('tenant_id', $record->id)->first();
                        if (!$tenantUser) {
                            Notification::make()->title('No user found for this tenant')->danger()->send();
                            return;
                        }

                        ImpersonationLog::create([
                            'super_admin_id' => auth()->id() ?? $tenantUser->id,
                            'tenant_id' => $record->id,
                            'impersonated_user_id' => $tenantUser->id,
                            'reason' => 'Super admin troubleshooting session',
                            'ip_address' => request()->ip(),
                        ]);

                        Auth::login($tenantUser);
                        session()->put('impersonated_by', auth()->id());

                        return redirect('/app');
                    }),
                Tables\Actions\Action::make('toggle_suspend')
                    ->label(fn (Tenant $record) => $record->status === 'suspended' ? 'Reactivate' : 'Suspend')
                    ->icon('heroicon-o-shield-exclamation')
                    ->color(fn (Tenant $record) => $record->status === 'suspended' ? 'success' : 'danger')
                    ->requiresConfirmation()
                    ->action(function (Tenant $record) {
                        $record->update([
                            'status' => $record->status === 'suspended' ? 'active' : 'suspended',
                        ]);
                        Notification::make()
                            ->title($record->status === 'active' ? 'Tenant reactivated' : 'Tenant suspended')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
